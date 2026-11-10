<?php
$assetController = (string) config('data.controlador');
$needsLadda = in_array($assetController, array('compras', 'configuraciones', 'importaciones', 'proveedores', 'recursoshumanos'), true);
$needsMoment = in_array($assetController, array('clientes', 'proveedores', 'ventas', 'utilidades', 'tributario'), true);
$needsDateRange = $assetController === 'tributario';
$needsChart = config('data.estadisticas') != null || in_array($assetController, array('admin', 'produccion-medica'), true);
$usesDataTables = config('data.table') != null && config('data.native_table') == null;
$canExportTables = Auth::check() && Auth::user()->isSystemUser();
$moduleScript = 'v3/sistema/' . config('data.carpeta') . '/functions_' . $assetController . '.js';
$moduleScriptPath = public_path($moduleScript);
$needsAuditMap = config('data.vista') === 'v3.contable.configuraciones.view_auditoria';
?>

@if (!$usesDataTables && config('data.native_table') == null)
    <script src="{{ url('v3/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
    <script>$.widget.bridge('uibutton', $.ui.button);</script>
@endif

<script src="{{ url('v3/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script>
    (function ($) {
        'use strict';

        // En las tablas V3 el icono de lista y la flecha forman un solo control.
        // Algunos módulos antiguos solo enlazaban la flecha con Bootstrap.
        $(document).off('click.v3TableActionIcon', '.btn-group > button.btn:first-child')
            .on('click.v3TableActionIcon', '.btn-group > button.btn:first-child', function (event) {
                var $button = $(this);
                var isActionIcon = $button.find('.fa-list, .fa-list-ul, .fa-list-ol, .fa-bars').length > 0;
                var $toggle = $button.siblings('button[data-toggle="dropdown"]').first();

                if (!isActionIcon || !$toggle.length || $button.is('[data-toggle="dropdown"]')) return;

                event.preventDefault();
                event.stopPropagation();
                $toggle.trigger('click');
            });
    })(jQuery);
</script>
<script src="{{ url('v3/plugins/bootstrap-switch/js/bootstrap-switch.min.js') }}?v={{ @filemtime(public_path('v3/plugins/bootstrap-switch/js/bootstrap-switch.min.js')) }}"></script>
<script src="{{ url('v3/plugins/select2/js/select2.full.min.js') }}"></script>
<script defer src="{{ \Sis_medico\Support\V3Asset::url('v3/dist/js/alphabetical-selects.js') }}"></script>

@if ($needsChart)
    <script src="{{ url('v3/plugins/Chart.js/Chart.min.js') }}"></script>
@endif

@if (config('data.estadisticas') != null)
    <script src="{{ url('v3/plugins/jqvmap/jquery.vmap.min.js') }}"></script>
    <script src="{{ url('v3/plugins/jqvmap/maps/jquery.vmap.usa.js') }}"></script>
    <script src="{{ url('v3/dist/js/pages/dashboard.js') }}"></script>
@endif

@if ($usesDataTables)
    <script src="{{ url('v3/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ url('v3/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ url('v3/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ url('v3/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    @if ($canExportTables)
        <script src="{{ url('v3/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
        <script src="{{ url('v3/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
        <script>
            window.AitrolTableExportAssets = {
                zip: @json(\Sis_medico\Support\V3Asset::url('v3/plugins/jszip/jszip.min.js')),
                pdf: @json(\Sis_medico\Support\V3Asset::url('v3/plugins/pdfmake/pdfmake.min.js')),
                fonts: @json(\Sis_medico\Support\V3Asset::url('v3/plugins/pdfmake/vfs_fonts.js')),
                error: @json(trans('v3/accessibility.export_load_error'))
            };
        </script>
        <script src="{{ url('v3/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
        <script src="{{ url('v3/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
        <script src="{{ url('v3/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
        <script src="{{ \Sis_medico\Support\V3Asset::url('v3/dist/js/datatables-export-policy.js') }}"></script>
    @endif
@endif

@if ($needsAuditMap)
    <link rel="stylesheet" href="{{ url('assets2/css/leaflet.css') }}?v={{ @filemtime(public_path('assets2/css/leaflet.css')) }}">
    <script src="{{ url('assets2/js/leaflet.js') }}?v={{ @filemtime(public_path('assets2/js/leaflet.js')) }}"></script>
@endif

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/shepherd.js@10.0.1/dist/css/shepherd.css">
<script src="https://cdn.jsdelivr.net/npm/shepherd.js@10.0.1/dist/js/shepherd.min.js"></script>

@if ($needsLadda)
    <script src="{{ url('v3/plugins/ladda/spin.min.js') }}"></script>
    <script src="{{ url('v3/plugins/ladda/ladda.min.js') }}"></script>
    <script src="{{ url('v3/plugins/ladda/ladda.jquery.min.js') }}"></script>
@endif

@if ($needsMoment)
    <script src="{{ url('v3/plugins/moment/moment.min.js') }}"></script>
@endif

@if ($needsDateRange)
    <script src="{{ url('v3/plugins/daterangepicker/daterangepicker.js') }}"></script>
@endif

<script src="{{ url('v3/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
<script src="{{ \Sis_medico\Support\V3Asset::url('v3/dist/js/adminlte.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>

@if (config('data.calendar') != null)
    <script src="{{ url('v3/plugins/fullcalendar/main.js') }}"></script>
    <script src="{{ url('v3/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js') }}"></script>
@endif

<script defer src="{{ \Sis_medico\Support\V3Asset::url('v3/sistema/functions.js') }}"></script>
<script defer src="{{ \Sis_medico\Support\V3Asset::url('v3/sistema/functions_tour.js') }}"></script>

@if (is_file($moduleScriptPath))
    <script defer src="{{ \Sis_medico\Support\V3Asset::url($moduleScript) }}"></script>
@endif
