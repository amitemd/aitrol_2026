<?php
$assetController = (string) config('data.controlador');
$needsLadda = in_array($assetController, array('compras', 'configuraciones', 'importaciones', 'proveedores', 'recursoshumanos'), true);
$needsDateRange = $assetController === 'tributario';
$usesDataTables = config('data.table') != null && config('data.native_table') == null;
$canExportTables = Auth::check() && Auth::user()->isSystemUser();
?>

<link rel="stylesheet" href="{{ url('v3/plugins/fontawesome-free/css/all.min.css') }}">
<link rel="stylesheet" href="{{ \Sis_medico\Support\V3Asset::url('v3/dist/css/botones.css') }}">
<link rel="stylesheet" href="{{ url('v3/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
<link rel="stylesheet" href="{{ url('v3/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ url('v3/plugins/flag-icon-css/css/flag-icon.min.css') }}">

@if ($usesDataTables)
    <link rel="stylesheet" href="{{ url('v3/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ url('v3/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    @if ($canExportTables)<link rel="stylesheet" href="{{ url('v3/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">@endif
@endif

@if (config('data.estadisticas') != null)
    <link rel="stylesheet" href="{{ url('v3/plugins/jqvmap/jqvmap.min.css') }}">
@endif

@if (config('data.calendar') != null)
    <link rel="stylesheet" href="{{ url('v3/plugins/fullcalendar/main.css') }}">
    <link rel="stylesheet" href="{{ url('v3/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css') }}">
@endif

@if ($needsLadda)
    <link rel="stylesheet" href="{{ url('v3/plugins/ladda/ladda-themeless.min.css') }}">
@endif

@if ($needsDateRange)
    <link rel="stylesheet" href="{{ url('v3/plugins/daterangepicker/daterangepicker.css') }}">
@endif

<link rel="stylesheet" href="{{ url('v3/plugins/bootstrap/css/bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ url('v3/plugins/select2/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ url('v3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ url('v3/dist/sweetalert2.min.css') }}">
<link rel="stylesheet" href="{{ url('v3/dist/css/adminlte.min.css') }}?v=3.2.0">
