  <html>
  <head>
    <title>ORDEN BIOPSIAS</title>
    <style type="text/css">
      #principal{
       width:100%;
      }

      @page { margin:45px 18px 150px; }

      .doctor-signature { margin-top:110px;text-align:center; }

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
      {
        page:WordSection1;
      }

      .parrafo2 p{
        margin: 0;
      }

      #bloq1{
        width:100%;
        padding-right:10px;
        box-sizing:border-box;
      }

      #bloq2{
        width:100%;
        padding-left:10px;
        box-sizing:border-box;
      }

      .table_wrapper{
        width:100%;margin:0px;padding:0px;table-layout:fixed;

      }

      .company-brand{
        margin:0 0 10px 0;
        text-align:center;
        font-family:Arial,sans-serif;
        height:/*AITROL_PDF_BRANDING_HEIGHT*/126px;
        line-height:0;
      }

      .company-brand img{width:430px;height:auto;max-width:95%;max-height:126px;vertical-align:middle;}
      .company-brand span{font-size:16px;font-weight:bold;color:#17324d;}

      .order-title-cell{
        background-color:#16b9b1;
        color:#fff;
        border-color:#16b9b1 !important;
        font-size:19px;
        text-align:center;
      }

      .section-title-cell{
        background-color:#16b9b1;
        color:#fff;
        border-color:#16b9b1 !important;
        font-size:17px;
        letter-spacing:.2px;
      }

      .recipe-watermark{
        position:fixed;
        left:0;
        right:0;
        top:275px;
        z-index:-1000;
        width:100%;
      }

      .recipe-watermark table,
      .recipe-footer table{width:100%;border-collapse:collapse;table-layout:fixed;}
      .recipe-watermark td{width:50%;text-align:center;vertical-align:middle;}
      .recipe-watermark img{width:500px;max-width:90%;height:auto;opacity:1;}

      .recipe-footer{
        position:fixed;
        left:0;
        right:0;
        bottom:-150px;
        height:150px;
        width:100%;
      }

      .recipe-footer td{width:50%;padding:0;vertical-align:bottom;}
      .recipe-footer img{display:block;width:100%;height:126px;max-height:126px;margin:0;}
    </style>
  </head>
  <body lang=ES-EC style="margin-top: 5px;margin-top:0px;padding-top:0px">
    @php
      $companyName = !empty($empresa) && !empty($empresa->nombrecomercial)
        ? $empresa->nombrecomercial
        : (!empty($empresa) && !empty($empresa->razonsocial) ? $empresa->razonsocial : 'ESTABLECIMIENTO DE SALUD');
    @endphp
    <div id="principal" style="margin-top:0px;padding-top:0px">
      <table class="table_wrapper"   cellpadding="0" cellspacing="0" style="margin-top:0px;padding-top:0px">
        <tr>
          <td style="width:50%;border-right:1px dashed #777" valign="top">
            <div id="bloq1">
              @php
                  $date = $historia->fecha_atencion;
              @endphp
              <div id="header">
                <div class="company-brand">
                  <!--AITROL_PDF_BRANDING_SLOT-->
                  <!--AITROL_PDF_BRANDING_FALLBACK_START-->
                    @if(!empty($recipeLogo))<img src="{{$recipeLogo}}" alt="{{$companyName}}">@endif
                  <!--AITROL_PDF_BRANDING_FALLBACK_END-->
                </div>
              </div>
              <br>
              <table  style="border: 1px solid; width: 100%;border-collapse: collapse; font-size: 14px;">
                <tbody>
                  <tr>
                    <td class="order-title-cell" style="border-right:1px solid;width:103px;"><b>ORDEN DE BIOPSIAS</b></td>
                  </tr>
                </tbody>
              </table>
                @php
                  if(!is_null($paciente)){
                    $seguro = Sis_medico\Seguro::find($paciente->id_seguro);
                  }
                @endphp
                @php
                  $fecha = substr($historia->fecha_atencion ?: $biop_first->created_at, 0, 10);
                  $invert = explode('-', $fecha);
                  $fecha_invert = count($invert) === 3 ? $invert[2]."/".$invert[1]."/".$invert[0] : $fecha;
                @endphp
                @php
                  /*
                  elseif(!is_null($biop_first->created_at))

                    $fecha = substr($biop_first->created_at,0,10);
                    $invert = explode( '-',$fecha);
                    $fecha_invert = $invert[2]."/".$invert[1]."/".$invert[0];*/
                  @endphp
                <table  style="border: 1px solid; width: 100%;border-collapse: collapse; font-size: 14px;">
                  <tbody>
                    <tr>
                      <td style="border-right: 1px solid;border-top: 1px solid;font-size: 16px"><b>&nbsp;FECHA:</b></td>
                      <td  colspan="1" style="border-right: 1px solid;font-size: 16px;">&nbsp;{{$fecha_invert}}</td>
                      <td style="border-right: 1px solid;border-top: 1px solid;font-size: 16px"><b>&nbsp;C.I</b></td>
                      <td  colspan="1" style="border-right: 1px solid;font-size: 16px;">&nbsp;{{$paciente->id}}</td>
                      <td colspan="1" style="border-right: 1px solid;font-size: 16px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>ORDEN BIOPSIAS</b></td>
                    </tr>
                    <tr>
                      <td style="border-right: 1px solid;border-top: 1px solid;font-size: 16px"><b>&nbsp;PACIENTE:</b></td>
                      <td colspan="4" style="border-right: 1px solid;border-top: 1px solid;font-size: 16px">&nbsp;{{$paciente->apellido1}} @if($paciente->apellido2!='(N/A)'){{$paciente->apellido2}}@endif {{$paciente->nombre1}} @if($paciente->nombre2!='(N/A)'){{$paciente->nombre2}}@endif</td>
                    </tr>
                    <tr>
                      <td style="border-right: 1px solid;border-top: 1px solid;font-size: 16px">&nbsp;<b>EDAD:</b></td>
                      <td colspan="2"style="border-right: 1px solid;border-top: 1px solid;font-size: 16px">&nbsp;{{$edad}}</td>
                      <td style="border-right: 1px solid;border-top: 1px solid;font-size: 16px">&nbsp;<b>SEGURO:</b></td>
                      <td colspan="1" style="border-right: 1px solid;border-top: 1px solid;font-size: 16px">&nbsp;{{optional($seguro)->nombre ?: 'PARTICULAR'}}</td>
                    </tr>
                    <tr>
                      <td class="section-title-cell" colspan="5" style="border-right:1px solid;border-top:1px solid;text-align:center;"><b>DETALLES FRASCOS</b></td>
                    </tr>
                    @foreach($impr_biospsias as $value)
                    <tr>
                      <td colspan="2" style="border-right: 2px solid;border-top: 1px solid;font-size: 16px;padding-left: 6px;"><p style="width: 100%; border: none; margin:0">Fco {{$value->numero_frasco}}: {{$value->descripcion_frasco}}</p></td>
                      <td colspan="3" style="border-right: 2px solid;border-top: 1px solid;font-size: 16px;padding-left: 6px,"><p style="width: 100%; border: none; margin:0">Obs: {{$value->observacion}}</p></td>
                    </tr>
                    @endforeach
                      <tr>
                        <td class="section-title-cell" colspan="5" style="border-right:1px solid;border-top:1px solid;"><b>&nbsp;CUADRO CLINICO:</b></td>
                      </tr>
                      <tr>
                        <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 16px;padding-left: 5px;padding-top: 5px"><?php echo $hc_proced->cuadro_clinico_bp ?></td>
                      </tr>
                      <tr>
                        <td class="section-title-cell" colspan="5" style="border-right:1px solid;border-top:1px solid;"><b>&nbsp;DIAGN&Oacute;STICO</b></td>
                      </tr>
                      <tr>
                       <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 16px;padding-left: 5px;padding-top: 5px"><?php echo $hc_proced->diagnosticos_bp ?>
                       </td>
                      </tr>
                  </tbody>
                </table>
                <div class="doctor-signature">
                  <p style="text-align: center;">
                    <hr style="width: 30%;margin-left: 202pt;margin:0 auto">
                    <label class="control-label" style="font-family: 'Helvetica general';font-size: 20px;">Doctor (a)
                    </label>
                  </p>
                </div>
            </div>
          </td>
          <td style="width:50%"  valign="top">
            <div id="bloq2">
              @php
                  $date = $historia->fecha_atencion;
              @endphp
              <div id="header">
                <div class="company-brand">
                  <!--AITROL_PDF_BRANDING_SLOT-->
                  <!--AITROL_PDF_BRANDING_FALLBACK_START-->
                    @if(!empty($recipeLogo))<img src="{{$recipeLogo}}" alt="{{$companyName}}">@endif
                  <!--AITROL_PDF_BRANDING_FALLBACK_END-->
                </div>
              </div>
              <br>
              <table  style="border: 1px solid; width: 100%;border-collapse: collapse; font-size: 14px;">
                <tbody>
                  <tr>
                    <td class="order-title-cell" style="border-right:1px solid;width:103px;"><b>ORDEN DE BIOPSIAS</b></td>
                  </tr>
                </tbody>
              </table>
                @php
                  if(!is_null($paciente)){
                    $seguro = Sis_medico\Seguro::find($paciente->id_seguro);
                  }
                @endphp
                @php
                  $fecha = substr($historia->fecha_atencion ?: $biop_first->created_at, 0, 10);
                  $invert = explode('-', $fecha);
                  $fecha_invert = count($invert) === 3 ? $invert[2]."/".$invert[1]."/".$invert[0] : $fecha;
                @endphp
                <table  style="border: 1px solid; width: 100%;border-collapse: collapse; font-size: 14px;">
                  <tbody>
                    <tr>
                      <td style="border-right: 1px solid;border-top: 1px solid;font-size: 16px"><b>&nbsp;FECHA:</b></td>
                      <td  colspan="1" style="border-right: 1px solid;font-size: 16px;">&nbsp;{{$fecha_invert}}</td>
                      <td style="border-right: 1px solid;border-top: 1px solid;font-size: 16px"><b>&nbsp;C.I</b></td>
                      <td  colspan="1" style="border-right: 1px solid;font-size: 16px;">&nbsp;{{$paciente->id}}</td>
                      <td colspan="1" style="border-right: 1px solid;font-size: 16px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>ORDEN BIOPSIAS</b></td>
                    </tr>
                    <tr>
                      <td style="border-right: 1px solid;border-top: 1px solid;font-size: 16px"><b>&nbsp;PACIENTE:</b></td>
                      <td colspan="4" style="border-right: 1px solid;border-top: 1px solid;font-size: 16px">&nbsp;{{$paciente->apellido1}} @if($paciente->apellido2!='(N/A)'){{$paciente->apellido2}}@endif {{$paciente->nombre1}} @if($paciente->nombre2!='(N/A)'){{$paciente->nombre2}}@endif</td>
                    </tr>
                    <tr>
                      <td style="border-right: 1px solid;border-top: 1px solid;font-size: 16px">&nbsp;<b>EDAD:</b></td>
                      <td colspan="2"style="border-right: 1px solid;border-top: 1px solid;font-size: 16px">&nbsp;{{$edad}}</td>
                      <td style="border-right: 1px solid;border-top: 1px solid;font-size: 16px">&nbsp;<b>SEGURO:</b></td>
                      <td colspan="1" style="border-right: 1px solid;border-top: 1px solid;font-size: 16px">&nbsp;{{optional($seguro)->nombre ?: 'PARTICULAR'}}</td>
                    </tr>
                    <tr>
                      <td class="section-title-cell" colspan="5" style="border-right:1px solid;border-top:1px solid;text-align:center;"><b>DETALLES FRASCOS</b></td>
                    </tr>
                    @foreach($impr_biospsias as $value)
                    <tr>
                      <td colspan="2" style="border-right: 2px solid;border-top: 1px solid;font-size: 16px;padding-left: 6px;"><p style="width: 100%; border: none; margin:0">Fco {{$value->numero_frasco}}: {{$value->descripcion_frasco}}</p></td>
                      <td colspan="3" style="border-right: 2px solid;border-top: 1px solid;font-size: 16px;padding-left: 6px,"><p style="width: 100%; border: none; margin:0">Obs: {{$value->observacion}}</p></td>
                    </tr>
                    @endforeach
                      <tr>
                        <td class="section-title-cell" colspan="5" style="border-right:1px solid;border-top:1px solid;"><b>&nbsp;CUADRO CLINICO:</b></td>
                      </tr>
                      <tr>
                        <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 16px;padding-left: 5px;padding-top: 5px"><?php echo $hc_proced->cuadro_clinico_bp ?></td>
                      </tr>
                      <tr>
                        <td class="section-title-cell" colspan="5" style="border-right:1px solid;border-top:1px solid;"><b>&nbsp;DIAGN&Oacute;STICO</b></td>
                      </tr>
                      <tr>
                       <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 16px;padding-left: 5px;padding-top: 5px"><?php echo $hc_proced->diagnosticos_bp ?></td>
                      </tr>
                  </tbody>
                </table>
                <div class="doctor-signature">
                  <p style="text-align: center;">
                    <hr style="width: 30%;margin-left: 202pt;margin:0 auto">
                    <label class="control-label" style="font-family: 'Helvetica general';font-size: 20px;">Doctor (a)
                    </label>
                  </p>
                </div>
            </div>
          </td>
        </tr>
      </table>
    </div>
    <div class="recipe-watermark">
      <table cellpadding="0" cellspacing="0">
        <tr>
          <td>@if(!empty($recipeWatermark))<img src="{{$recipeWatermark}}" alt="">@endif</td>
          <td>@if(!empty($recipeWatermark))<img src="{{$recipeWatermark}}" alt="">@endif</td>
        </tr>
      </table>
    </div>
    <div class="recipe-footer">
      <table cellpadding="0" cellspacing="0">
        <tr>
          <td>@if(!empty($recipeFooter))<img src="{{$recipeFooter}}" alt="">@endif</td>
          <td>@if(!empty($recipeFooter))<img src="{{$recipeFooter}}" alt="">@endif</td>
        </tr>
      </table>
    </div>
  </body>
</html>
