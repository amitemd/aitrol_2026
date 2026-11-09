<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>{{isset($pdfOrderTitle) ? $pdfOrderTitle : 'Orden de imágenes'}}</title>
  <style>
    @page { margin: 34px 42px 120px; }

    * { box-sizing: border-box; }

    body {
      margin: 0;
      padding: 0;
      color: #26313a;
      font-family: Arial, Helvetica, sans-serif;
      font-size: 11px;
      line-height: 1.25;
    }

    .order-copy { width: 100%; }

    .company-brand {
      height: /*AITROL_PDF_BRANDING_HEIGHT*/112px;
      margin: 0 0 8px;
      text-align: center;
      line-height: 0;
    }

    .company-brand img {
      width: 400px;
      height: auto;
      max-width: 90%;
      max-height: 112px;
      vertical-align: middle;
    }

    .company-brand span {
      color: #17324d;
      font-size: 18px;
      font-weight: bold;
      line-height: 1.2;
    }

    .order-table,
    .procedure-group {
      width: 100%;
      border-collapse: collapse;
      table-layout: fixed;
    }

    .order-table {
      border: 1px solid #707982;
    }

    .order-table td {
      padding: 4px 6px;
      border-top: 1px solid #a5adb3;
      border-right: 1px solid #a5adb3;
      vertical-align: top;
      overflow-wrap: anywhere;
    }

    .order-table tr:first-child td { border-top: 0; }
    .order-table td:last-child { border-right: 0; }

    .order-title {
      padding: 5px 8px !important;
      border: 0 !important;
      background-color: #16b9b1;
      color: #fff;
      font-size: 16px;
      font-weight: bold;
      text-align: center;
    }

    .label {
      width: 14%;
      font-weight: bold;
      white-space: nowrap;
    }

    .section-title {
      padding: 4px 7px !important;
      border-color: #16b9b1 !important;
      background-color: #16b9b1;
      color: #fff;
      font-size: 12px;
      font-weight: bold;
      letter-spacing: .2px;
    }

    .section-content {
      min-height: 25px;
      padding: 6px 8px !important;
      font-size: 11.5px;
    }

    .section-content p { margin: 0; }
    .muted { color: #66727b; font-style: italic; }

    .procedures-title {
      margin-top: 8px;
      padding: 5px 8px;
      background-color: #16b9b1;
      color: #fff;
      font-size: 13px;
      font-weight: bold;
      letter-spacing: .2px;
    }

    .procedure-group {
      margin-top: 6px;
      border: 1px solid #b8d9d7;
      page-break-inside: avoid;
    }

    .procedure-group__title {
      padding: 5px 8px;
      background-color: #d9f4f1;
      color: #155e5a;
      font-size: 11.5px;
      font-weight: bold;
      text-transform: uppercase;
    }

    .procedure-group__title span {
      float: right;
      color: #4f7774;
      font-size: 9.5px;
      font-weight: normal;
      text-transform: none;
    }

    .exam-cell {
      width: 50%;
      padding: 6px 8px;
      border-top: 1px solid #dce8e7;
      border-right: 1px solid #dce8e7;
      background-color: rgba(255,255,255,.86);
      color: #34424d;
      font-size: 11px;
      vertical-align: middle;
    }

    .exam-cell:last-child { border-right: 0; }

    .exam-marker {
      display: inline-block;
      width: 7px;
      height: 7px;
      margin-right: 7px;
      border-radius: 50%;
      background-color: #79005f;
      vertical-align: 1px;
    }

    .doctor-signature {
      margin-top: 34px;
      text-align: center;
    }

    .doctor-signature img {
      display: block;
      width: 135px;
      height: 52px;
      margin: 0 auto -3px;
      object-fit: contain;
    }

    .doctor-signature hr {
      width: 30%;
      margin: 0 auto 4px;
      border: 0;
      border-top: 1px solid #333;
    }

    .doctor-signature strong { display: block; font-size: 11px; }
    .doctor-signature span { display: block; font-size: 9.5px; color: #555; }

    .recipe-watermark {
      position: fixed;
      z-index: -1000;
      top: 250px;
      right: 0;
      left: 0;
      width: 100%;
      text-align: center;
    }

    .recipe-watermark img {
      width: 430px;
      max-width: 72%;
      height: auto;
    }

    .recipe-footer {
      position: fixed;
      right: -8px;
      bottom: -118px;
      left: -8px;
      height: 112px;
    }

    .recipe-footer img {
      display: block;
      width: 100%;
      height: 112px;
      margin: 0;
    }
  </style>
</head>
<body>
  @php
    $fechaOrden = substr((string) $orden_proc_imagenes->fecha_orden, 0, 10);
    $partesFecha = explode('-', $fechaOrden);
    $fechaInvertida = count($partesFecha) === 3
      ? $partesFecha[2] . '/' . $partesFecha[1] . '/' . $partesFecha[0]
      : $fechaOrden;

    $partesPaciente = array_filter(array(
      $paciente->apellido1,
      $paciente->apellido2 !== '(N/A)' ? $paciente->apellido2 : null,
      $paciente->nombre1,
      $paciente->nombre2 !== '(N/A)' ? $paciente->nombre2 : null,
    ));
    $nombrePaciente = implode(' ', $partesPaciente);

    $partesDoctor = array_filter(array(
      optional($doctor_solicitante)->apellido1,
      optional($doctor_solicitante)->apellido2,
      optional($doctor_solicitante)->nombre1,
      optional($doctor_solicitante)->nombre2,
    ));
    $nombreDoctor = implode(' ', $partesDoctor);
    $companyName = !empty($empresa) && !empty($empresa->nombrecomercial)
      ? $empresa->nombrecomercial
      : (!empty($empresa) && !empty($empresa->razonsocial) ? $empresa->razonsocial : 'ESTABLECIMIENTO DE SALUD');
    $mostrarAntecedentes = trim(strip_tags((string) $paciente->antecedentes_pat)) !== ''
      || trim(strip_tags((string) $paciente->antecedentes_fam)) !== ''
      || trim(strip_tags((string) $paciente->antecedentes_quir)) !== '';
  @endphp

  <div class="order-copy">
    <div class="company-brand">
      <!--AITROL_PDF_BRANDING_SLOT-->
      <!--AITROL_PDF_BRANDING_FALLBACK_START-->
        @if(!empty($recipeLogo))
          <img src="{{$recipeLogo}}" alt="{{$companyName}}">
        @else
          <span>{{$companyName}}</span>
        @endif
      <!--AITROL_PDF_BRANDING_FALLBACK_END-->
    </div>

    <table class="order-table" cellpadding="0" cellspacing="0">
      <tr>
        <td colspan="6" class="order-title">{{isset($pdfOrderTitle) ? $pdfOrderTitle : 'ORDEN DE PROCEDIMIENTOS DE IMAGEN'}}</td>
      </tr>
      <tr>
        <td class="label">FECHA:</td>
        <td style="width:22%">{{$fechaInvertida}}</td>
        <td class="label" style="width:9%">C.I.:</td>
        <td style="width:22%">{{$paciente->id}}</td>
        <td class="label" style="width:10%">EDAD:</td>
        <td style="width:23%">{{$edad}}</td>
      </tr>
      <tr>
        <td class="label">PACIENTE:</td>
        <td colspan="5">{{$nombrePaciente}}</td>
      </tr>
      <tr>
        <td class="label">DOCTOR:</td>
        <td colspan="3">{{$nombreDoctor ?: 'NO REGISTRADO'}}</td>
        <td class="label">SEGURO:</td>
        <td>{{optional($seguro)->nombre ?: 'PARTICULAR'}}</td>
      </tr>

      @if($mostrarAntecedentes)
        <tr><td colspan="6" class="section-title">ANTECEDENTES</td></tr>
        @if(trim(strip_tags((string) $paciente->antecedentes_pat)) !== '')
          <tr><td class="label">PATOLÓGICOS:</td><td colspan="5" class="section-content">{!! $paciente->antecedentes_pat !!}</td></tr>
        @endif
        @if(trim(strip_tags((string) $paciente->antecedentes_fam)) !== '')
          <tr><td class="label">FAMILIARES:</td><td colspan="5" class="section-content">{!! $paciente->antecedentes_fam !!}</td></tr>
        @endif
        @if(trim(strip_tags((string) $paciente->antecedentes_quir)) !== '')
          <tr><td class="label">QUIRÚRGICOS:</td><td colspan="5" class="section-content">{!! $paciente->antecedentes_quir !!}</td></tr>
        @endif
      @endif

      <tr><td colspan="6" class="section-title">MOTIVO</td></tr>
      <tr><td colspan="6" class="section-content">{!! $orden_proc_imagenes->motivo_consulta ?: '<span class="muted">Sin información</span>' !!}</td></tr>
    </table>

    <div class="procedures-title">{{isset($pdfProceduresTitle) ? $pdfProceduresTitle : 'EXÁMENES SOLICITADOS'}}</div>

    @forelse($procedimientosImagenes as $procedimientosGrupo)
      @php
        $grupoProcedimiento = optional($procedimientosGrupo->first())->grupo_procedimiento;
        $nombreGrupo = optional($grupoProcedimiento)->nombre ?: (isset($pdfDefaultGroupName) ? $pdfDefaultGroupName : 'IMÁGENES');
      @endphp
      <table class="procedure-group" cellpadding="0" cellspacing="0">
        <tr>
          <td colspan="2" class="procedure-group__title">
            {{$nombreGrupo}}
            <span>{{$procedimientosGrupo->count()}} {{$procedimientosGrupo->count() === 1 ? (isset($pdfItemSingular) ? $pdfItemSingular : 'examen') : (isset($pdfItemPlural) ? $pdfItemPlural : 'exámenes')}}</span>
          </td>
        </tr>
        @foreach($procedimientosGrupo->values()->chunk(2) as $filaExamenes)
          @php $filaExamenes = $filaExamenes->values(); @endphp
          <tr>
            @if($filaExamenes->count() === 1)
              <td colspan="2" class="exam-cell"><span class="exam-marker"></span>{{$filaExamenes->first()->nombre}}</td>
            @else
              <td class="exam-cell"><span class="exam-marker"></span>{{$filaExamenes->get(0)->nombre}}</td>
              <td class="exam-cell"><span class="exam-marker"></span>{{$filaExamenes->get(1)->nombre}}</td>
            @endif
          </tr>
        @endforeach
      </table>
    @empty
      <table class="procedure-group" cellpadding="0" cellspacing="0">
        <tr><td class="exam-cell muted">{{isset($pdfEmptyMessage) ? $pdfEmptyMessage : 'Sin exámenes registrados'}}</td></tr>
      </table>
    @endforelse

    <table class="order-table" cellpadding="0" cellspacing="0" style="margin-top:8px">
      <tr><td class="section-title">OBSERVACIÓN MÉDICA</td></tr>
      <tr><td class="section-content">{!! $orden_proc_imagenes->observacion_medica ?: '<span class="muted">Sin observaciones</span>' !!}</td></tr>
    </table>

    <div class="doctor-signature">
      @if(!empty($firmaImagen))<img src="{{$firmaImagen}}" alt="Firma médica">@endif
      <hr>
      <strong>Doctor (a)</strong>
      @if($nombreDoctor)<span>{{$nombreDoctor}}</span>@endif
    </div>
  </div>

  <div class="recipe-watermark">
    @if(!empty($recipeWatermark))<img src="{{$recipeWatermark}}" alt="">@endif
  </div>

  <div class="recipe-footer">
    @if(!empty($recipeFooter))<img src="{{$recipeFooter}}" alt="">@endif
  </div>
</body>
</html>
