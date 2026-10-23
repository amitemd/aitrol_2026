<?php
if (config('data.table') == null) {
?>
    <script src="{{ url('v3/') }}/plugins/jquery-ui/jquery-ui.min.js"></script>
    <script>
        $.widget.bridge('uibutton', $.ui.button)
    </script>
<?php
}
?>
<script src="{{ url('v3/') }}/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="{{ url('v3/') }}/plugins/select2/js/select2.full.min.js"></script>
<script src="{{ url('v3/') }}/plugins/Chart.js/Chart.min.js"></script>
<!--<script src="{{ url('v3/') }}/plugins/sparklines/sparkline.js"></script>-->
<?php
if (config('data.estadisticas') != null) {
?>
    <script src="{{ url('v3/') }}/plugins/jqvmap/jquery.vmap.min.js"></script>
    <script src="{{ url('v3/') }}/plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
    <script src="{{ url('v3/') }}/dist/js/pages/dashboard.js"></script>
<?php
}
if (config('data.table') != null) {
?>
    <script src="{{ url('v3/') }}/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="{{ url('v3/') }}/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="{{ url('v3/') }}/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="{{ url('v3/') }}/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="{{ url('v3/') }}/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="{{ url('v3/') }}/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    <script src="{{ url('v3/') }}/plugins/jszip/jszip.min.js"></script>
    <script src="{{ url('v3/') }}/plugins/pdfmake/pdfmake.min.js"></script>
    <script src="{{ url('v3/') }}/plugins/pdfmake/vfs_fonts.js"></script>
    <script src="{{ url('v3/') }}/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
    <script src="{{ url('v3/') }}/plugins/datatables-buttons/js/buttons.print.min.js"></script>
    <script src="{{ url('v3/') }}/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<?php
}
?>
<script src="{{ url('v3/') }}/plugins/popper/popper.js"></script>
<script src="https://cdn.jsdelivr.net/npm/shepherd.js@10.0.1/dist/js/shepherd.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/shepherd.js@10.0.1/dist/css/shepherd.css" />
<script src="https://kit.fontawesome.com/0239797fda.js" crossorigin="anonymous"></script>
<script src="https://unpkg.com/@popperjs/core@2"></script>
<script src="{{ url('/v3') }}/plugins/ladda/spin.min.js"></script>
<script src="{{ url('/v3') }}/plugins/ladda/ladda.min.js"></script>
<script src="{{ url('/v3') }}/plugins/ladda/ladda.jquery.min.js"></script>
<script src="{{ url('v3/') }}/plugins/jquery-knob/jquery.knob.min.js"></script>
<script src="{{ url('v3/') }}/plugins/moment/moment.min.js"></script>
<script src="{{ url('v3/') }}/plugins/daterangepicker/daterangepicker.js"></script>
<script src="{{ url('v3/') }}/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<script src="{{ url('v3/') }}/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
<script src="{{ url('v3/') }}/plugins/summernote/summernote-bs4.min.js"></script>
<script src="{{ url('v3/') }}/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<script src="{{ url('v3/') }}/dist/js/adminlte.js?v=3.2.0"></script>
<script src="{{ url('v3/') }}/dist/js/demo.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
<?php
if (config('data.calendar') != null) {
?>
    <script src="{{ url('v3/') }}/plugins/fullcalendar/main.js"></script>
    <script src="{{ url('v3/') }}/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js"></script>
<?php
}
?>
<script async src="{{ url('v3/') }}/sistema/functions.js"></script>
<script src="{{ url('v3/') }}/sistema/functions_tour.js"></script>
<script async src="{{ url('v3/') }}/sistema/{{config('data.carpeta')}}/functions_{{ config('data.controlador') }}.js"></script>
<?php
if (config('data.maps') != null) {
?>
    <script async src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAnJuy3GJC6o7Jw9EJsPekeoOVLes3xqno" defer></script>
<?php
}
?>