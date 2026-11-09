@php
  $orden_proc_imagenes = $orden_proc_endoscopico;
  $procedimientosImagenes = $procedimientosEndoscopicos;
  $pdfOrderTitle = 'ORDEN DE PROCEDIMIENTO ENDOSCÓPICO';
  $pdfProceduresTitle = 'PROCEDIMIENTOS SOLICITADOS';
  $pdfDefaultGroupName = 'PROCEDIMIENTOS';
  $pdfItemSingular = 'procedimiento';
  $pdfItemPlural = 'procedimientos';
  $pdfEmptyMessage = 'Sin procedimientos registrados';
@endphp

@include('hc4.ordenes.orden_procedimiento_imagenes.pdf_orden_imagenes')
