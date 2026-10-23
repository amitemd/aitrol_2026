@extends('laboratorio.orden.base')
@section('action-content')

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset('plugins/iCheck/all.css') }}">
@php
  $rolUsuario = Auth::user()->id_tipo_usuario;
  $usuario = Auth::user()->id;
  $empresa_labs= Sis_medico\Empresa::where('prioridad_labs', '1')->first();
  $seguimiento_labels = [
    'pendiente_llamar' => trans('orden_ui.pending_call'),
    'contactado' => trans('orden_ui.contacted'),
    'confirmo_asistencia' => trans('orden_ui.attendance_confirmed'),
    'no_vendra' => trans('orden_ui.will_not_attend'),
  ];
@endphp

<style type="text/css">
.table>tbody>tr>td, .table>tbody>tr>th {
    padding: 1px;
}

#example2,
#example2 thead tr th,
#example2 tbody tr td {
    color: #000000 !important;
}

/* Texto normal dentro de las celdas */
#example2 tbody tr td span:not(.label):not(.glyphicon):not(.caret),
#example2 tbody tr td div:not(.progress):not(.progress-bar):not(.btn-group),
#example2 tbody tr td small,
#example2 tbody tr td strong {
    color: #000000 !important;
}

/* Mantener visibles los textos de botones y etiquetas */
#example2 tbody tr td .btn,
#example2 tbody tr td .btn span,
#example2 tbody tr td .label,
#example2 tbody tr td .dropdown-menu li a,
#example2 tbody tr td .dropdown-menu li a span {
    color: #ffffff !important;
}

/* El porcentaje dentro del progress debe verse negro */
#example2 tbody tr td .progress-bar span {
    color: #000000 !important;
}
.dropdown-menu>li>a{
    color:white !important;
    padding-left: 3px !important;
    padding-right: 3px !important;
    font-size: 12px !important;
  }
 
  .dropdown-menu>li>a:hover{
    background-color:#008d4c !important;
  }
  .cot>li>a:hover{
    background-color:#00acd6 !important;
  }
  .form-group{
    margin-bottom: 2px;
  }
  .hovers:hover{
    cursor: pointer;
  }

.orden-lab-modern {
    background: #f4f7fb;
    padding: 16px 18px;
}

.orden-lab-modern .box {
    border: 0;
    border-radius: 16px;
    box-shadow: 0 10px 28px rgba(15, 23, 42, .08);
    background: #ffffff;
    overflow: hidden;
}

.orden-lab-modern .box-header {
    padding: 18px 22px 10px;
    border-bottom: 1px solid #eef2f7;
    background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
}

.orden-lab-modern .box-header h4 {
    margin: 7px 0;
    color: #0f2742;
    font-size: 24px;
    font-weight: 700;
    letter-spacing: -.3px;
}

.orden-lab-modern .box-header h4:before {
    content: "\f0c3";
    font-family: FontAwesome;
    display: inline-block;
    width: 38px;
    height: 38px;
    margin-right: 10px;
    border-radius: 11px;
    color: #0876b9;
    background: #e8f4ff;
    text-align: center;
    line-height: 38px;
    font-size: 18px;
}

.orden-lab-modern .box-body {
    padding: 8px 12px 20px;
}

.orden-lab-modern form {
    margin: -2px 0 12px;
    padding: 14px 16px 12px;
    border: 1px solid #e7edf5;
    border-radius: 16px;
    background: #ffffff;
    box-shadow: 0 8px 22px rgba(15, 23, 42, .06);
}

@media (min-width: 992px) {
    .orden-lab-modern .filtros-principales {
        display: flex;
        align-items: flex-end;
        width: 100%;
    }

    .orden-lab-modern .filtros-principales > .form-group {
        float: none;
        flex: 0 0 20%;
        width: 20%;
    }
}

.orden-lab-modern label.control-label,
.orden-lab-modern label {
    color: #18243a;
    font-size: 12px;
    font-weight: 700;
    margin-bottom: 6px;
}

.orden-lab-modern .form-control,
.orden-lab-modern .select2-container--default .select2-selection--single {
    height: 34px !important;
    border: 1px solid #d9e2ef !important;
    border-radius: 8px !important;
    box-shadow: none !important;
    color: #111827 !important;
    background: #ffffff !important;
    font-size: 12px;
}

.orden-lab-modern .form-control:focus,
.orden-lab-modern .select2-container--default.select2-container--focus .select2-selection--single {
    border-color: #1f8fd1 !important;
    box-shadow: 0 0 0 3px rgba(31, 143, 209, .12) !important;
}

.orden-lab-modern .input-group-addon {
    border-color: #d9e2ef;
    background: #f8fafc;
    color: #334155;
    border-radius: 8px;
}

.orden-lab-modern .btn {
    border: 0;
    border-radius: 9px;
    font-weight: 700;
    letter-spacing: -.1px;
    box-shadow: 0 6px 14px rgba(15, 23, 42, .10);
    transition: all .18s ease-in-out;
}

.orden-lab-modern .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 10px 20px rgba(15, 23, 42, .16);
}

.orden-lab-modern .btn-primary {
    background: linear-gradient(135deg, #2f80ed, #1f65d6) !important;
}

.orden-lab-modern .btn-warning {
    background: linear-gradient(135deg, #ff9f1c, #f97316) !important;
    color: #ffffff !important;
}

.orden-lab-modern .btn-success {
    background: linear-gradient(135deg, #12b76a, #079455) !important;
}

.orden-lab-modern .btn-danger {
    background: linear-gradient(135deg, #ff5c5c, #e03131) !important;
}

.orden-lab-modern .btn-info {
    background: linear-gradient(135deg, #38bdf8, #0284c7) !important;
}

.orden-lab-modern .btn-sm {
    min-height: 34px;
}

.orden-lab-modern .btn-xs {
    border-radius: 7px;
    padding: 4px 7px;
    box-shadow: none;
}

.orden-lab-modern .box-header .btn {
    min-height: 38px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    white-space: normal;
}

.orden-lab-modern form .btn {
    white-space: normal;
}

.orden-lab-modern form .col-md-4 .btn,
.orden-lab-modern form .col-xs-4 .btn {
    min-height: 36px;
}

.orden-lab-modern form .col-md-4 .btn:after,
.orden-lab-modern .box-header .btn:after {
    content: "\f105";
    font-family: FontAwesome;
    float: right;
    margin-left: 8px;
    opacity: .85;
}

.pendientes-panel {
    margin: 8px 0 12px;
    padding: 12px 14px !important;
    border: 1px solid #fecaca;
    border-radius: 12px;
    background: #fff1f2;
    color: #dc2626 !important;
    font-size: 13px;
}

.pendientes-panel b:before {
    content: "\f071";
    font-family: FontAwesome;
    margin-right: 7px;
}

.orden-lab-modern #example2_wrapper {
    padding: 14px;
    border: 1px solid #e7edf5;
    border-radius: 16px;
    background: #ffffff;
    box-shadow: 0 8px 22px rgba(15, 23, 42, .06);
}

.orden-lab-modern .table-responsive {
    border-radius: 14px;
    border: 1px solid #edf2f7;
    background: #ffffff;
}

.orden-lab-modern #example2 {
    margin-bottom: 0 !important;
    border-collapse: separate !important;
    border-spacing: 0;
    color: #111827 !important;
    font-size: 12px !important;
}

.orden-lab-modern #example2 thead tr th {
    position: sticky;
    top: 0;
    z-index: 4;
    padding: 11px 9px !important;
    border-bottom: 1px solid #e5edf6 !important;
    border-right: 0 !important;
    border-left: 0 !important;
    background: #ffffff !important;
    color: #111827 !important;
    font-size: 12px;
    font-weight: 800;
    vertical-align: middle;
    white-space: nowrap;
}

.orden-lab-modern #example2 tbody tr td {
    padding: 10px 9px !important;
    border-top: 1px solid #eef2f7 !important;
    border-right: 0 !important;
    border-left: 0 !important;
    color: #111827 !important;
    vertical-align: middle;
    background: transparent;
}

.orden-lab-modern #example2 tbody tr {
    transition: background .16s ease, transform .16s ease;
}

.orden-lab-modern #example2 tbody tr:hover {
    background: #f8fbff !important;
}

.orden-lab-modern #example2 tbody tr[style*="A4F4CF"],
.orden-lab-modern #example2 tbody tr[style*="#A4F4CF"],
.orden-lab-modern #example2 tbody tr[style*="background-color: #A4F4CF"] {
    background: #ecfdf5 !important;
}

.orden-lab-modern #example2 tbody tr[style*="FFC9C9"],
.orden-lab-modern #example2 tbody tr[style*="#FFC9C9"],
.orden-lab-modern #example2 tbody tr[style*="background-color: #FFC9C9"] {
    background: #fff1f2 !important;
}

.orden-lab-modern #example2 tbody tr:nth-child(even):not([style]) {
    background: #fbfdff;
}

.orden-lab-modern #example2 input[type="checkbox"] {
    width: 16px !important;
    height: 16px !important;
    margin: 0;
    vertical-align: middle;
    accent-color: #0ea5e9;
}

.orden-lab-modern .label {
    display: inline-block;
    min-width: auto;
    padding: 4px 7px;
    border-radius: 7px;
    font-size: 10px !important;
    font-weight: 800;
    box-shadow: none;
}

.orden-lab-modern .bg-green {
    background: #dcfce7 !important;
    color: #15803d !important;
    border: 1px solid #86efac;
}

.orden-lab-modern .bg-red {
    background: #fee2e2 !important;
    color: #b91c1c !important;
    border: 1px solid #fecaca;
}

.orden-lab-modern .bg-primary {
    background: #dbeafe !important;
    color: #1d4ed8 !important;
    border: 1px solid #bfdbfe;
}

.orden-lab-modern .progress {
    height: 8px;
    margin: 0 0 5px;
    border-radius: 999px;
    background: #eef2f7;
    box-shadow: none;
    overflow: hidden;
}

.orden-lab-modern .progress-bar {
    border-radius: 999px;
    box-shadow: none;
}

.orden-lab-modern .progress-bar span {
    display: block;
    position: relative;
    top: 10px;
    color: #111827 !important;
    font-size: 10px;
    font-weight: 700;
}

.orden-lab-modern .btn-group {
    display: inline-flex;
    flex-wrap: wrap;
    gap: 2px;
}

.orden-lab-modern .dropdown-menu {
    border: 0;
    border-radius: 10px;
    box-shadow: 0 14px 30px rgba(15, 23, 42, .20);
    overflow: hidden;
}

.orden-lab-modern .dropdown-menu>li>a {
    padding: 7px 9px !important;
    color: #ffffff !important;
}

.orden-lab-modern .dataTables_info,
.orden-lab-modern #example2_info {
    padding: 12px 4px;
    color: #475569;
    font-weight: 600;
}

.orden-lab-modern .pagination > li > a,
.orden-lab-modern .pagination > li > span {
    margin: 0 3px;
    border: 1px solid #e2e8f0;
    border-radius: 8px !important;
    color: #334155;
    font-weight: 700;
}

.orden-lab-modern .pagination > .active > span,
.orden-lab-modern .pagination > .active > a {
    background: #0ea5e9 !important;
    border-color: #0ea5e9 !important;
    color: #ffffff !important;
}

/* Mantener textos negros dentro de la tabla */
.orden-lab-modern #example2 tbody td,
.orden-lab-modern #example2 tbody td span:not(.label):not(.glyphicon):not(.caret),
.orden-lab-modern #example2 tbody td div:not(.progress):not(.progress-bar):not(.btn-group),
.orden-lab-modern #example2 tbody td small,
.orden-lab-modern #example2 tbody td strong {
    color: #111827 !important;
}

/* Mantener blancos los textos internos de botones */
.orden-lab-modern #example2 tbody td .btn,
.orden-lab-modern #example2 tbody td .btn span,
.orden-lab-modern #example2 tbody td .dropdown-menu li a,
.orden-lab-modern #example2 tbody td .dropdown-menu li a span {
    color: #ffffff !important;
}

@media (max-width: 991px) {
    .orden-lab-modern {
        padding: 10px;
    }

    .orden-lab-modern .box-header,
    .orden-lab-modern .box-body {
        padding-left: 12px;
        padding-right: 12px;
    }

    .orden-lab-modern #example2 {
        min-width: 1200px;
    }
}


/* =========================================================
   CORRECCIONES VISUALES SOLICITADAS
   1) Quitar línea blanca detrás de filtros
   2) Estado con letras negras
   3) Barra de progreso más grande con porcentaje adentro
   ========================================================= */

/* Quita la línea / caja blanca interna que aparece detrás de los filtros */
.orden-lab-modern form {
    border: 0 !important;
    box-shadow: none !important;
    background: transparent !important;
    padding-top: 8px !important;
}

/* Evita que algún borde superior del bloque de filtros se vea como línea */
.orden-lab-modern .form-row,
.orden-lab-modern form > .form-row,
.orden-lab-modern form > .form-row > .col-md-12 {
    border: 0 !important;
    box-shadow: none !important;
    background: transparent !important;
}

/* Estado: letras negras, especialmente el label Pag. */
.orden-lab-modern #example2 tbody td .label,
.orden-lab-modern #example2 tbody td .label.bg-green,
.orden-lab-modern #example2 tbody td .label.bg-red,
.orden-lab-modern #example2 tbody td .label.bg-primary {
    color: #000000 !important;
    text-shadow: none !important;
}

/* Mantener fondos suaves de estados */
.orden-lab-modern #example2 tbody td .label.bg-green {
    background: #dcfce7 !important;
    border: 1px solid #86efac !important;
}

.orden-lab-modern #example2 tbody td .label.bg-red {
    background: #fee2e2 !important;
    border: 1px solid #fecaca !important;
}

.orden-lab-modern #example2 tbody td .label.bg-primary {
    background: #dbeafe !important;
    border: 1px solid #bfdbfe !important;
}

.tabla-ordenes th.col-fecha-creacion,
.tabla-ordenes td.col-fecha-creacion,
.tabla-ordenes th.col-dias-transcurridos,
.tabla-ordenes td.col-dias-transcurridos {
    text-align: center !important;
    vertical-align: middle !important;
}

.tabla-ordenes th.col-fecha-creacion,
.tabla-ordenes td.col-fecha-creacion {
    min-width: 115px;
}

.tabla-ordenes th.col-dias-transcurridos,
.tabla-ordenes td.col-dias-transcurridos {
    min-width: 105px;
}

.dias-transcurridos-badge {
    display: inline-block;
    padding: 4px 7px;
    border: 1px solid transparent;
    border-radius: 7px;
    color: #000000 !important;
    font-size: 10px;
    font-weight: 700;
    white-space: nowrap;
}

.dias-transcurridos-hoy {
    background: #dcfce7;
    border-color: #86efac;
}

.dias-transcurridos-advertencia {
    background: #fef9c3;
    border-color: #facc15;
}

.dias-transcurridos-atencion {
    background: #ffedd5;
    border-color: #fb923c;
}

.dias-transcurridos-urgente {
    background: #fee2e2;
    border-color: #f87171;
}

.seguimiento-badge {
    display: inline-block;
    padding: 4px 7px;
    border-radius: 7px;
    font-size: 10px;
    font-weight: 700;
    white-space: normal;
}

.tabla-ordenes th.col-seguimiento,
.tabla-ordenes td.col-seguimiento {
    text-align: center !important;
    vertical-align: middle !important;
}

.tabla-ordenes td.col-seguimiento .seguimiento-badge {
    text-align: center;
    margin: 0 auto;
}

.tabla-ordenes th.col-acciones,
.tabla-ordenes td.col-acciones {
    text-align: center !important;
    vertical-align: middle !important;
    min-width: 112px;
}

.acciones-orden-stack {
    display: flex;
    flex-direction: column;
    align-items: stretch;
    gap: 5px;
    width: 104px;
    margin: 0 auto;
}

.acciones-orden-stack + .acciones-orden-stack {
    margin-top: 5px;
}

.acciones-orden-stack .btn-accion-orden {
    display: flex !important;
    align-items: center;
    justify-content: center;
    gap: 4px;
    width: 104px !important;
    height: 28px !important;
    min-height: 28px !important;
    margin: 0 !important;
    padding: 3px 6px !important;
    line-height: 20px !important;
    white-space: nowrap !important;
    overflow: hidden;
}

.btn-group.btn-documento-uniforme {
    display: flex !important;
    flex-wrap: nowrap !important;
    width: 86px !important;
    height: 24px;
    margin-bottom: 4px;
    vertical-align: top;
}

.btn-group.btn-documento-uniforme .btn-documento-principal {
    display: inline-flex;
    float: none !important;
    flex: 0 0 64px;
    box-sizing: border-box;
    align-items: center;
    justify-content: center;
    width: 64px !important;
    height: 24px;
    padding: 2px !important;
    line-height: 18px;
    white-space: nowrap;
}

.btn-group.btn-documento-uniforme > .dropdown-toggle {
    display: inline-flex;
    float: none !important;
    flex: 0 0 22px;
    box-sizing: border-box;
    align-items: center;
    justify-content: center;
    width: 22px !important;
    height: 24px;
    padding: 2px !important;
}

.seguimiento-pendiente_llamar {
    color: #92400e !important;
    background: #fef3c7;
    border: 1px solid #f59e0b;
}

.seguimiento-contactado {
    color: #1d4ed8 !important;
    background: #dbeafe;
    border: 1px solid #60a5fa;
}

.seguimiento-confirmo_asistencia {
    color: #166534 !important;
    background: #dcfce7;
    border: 1px solid #86efac;
}

.seguimiento-no_vendra {
    color: #991b1b !important;
    background: #fee2e2;
    border: 1px solid #f87171;
}

#modal_seguimiento_orden .modal-header {
    position: relative;
}

#modal_seguimiento_orden .modal-title {
    text-align: center;
    font-weight: bold;
}

#modal_seguimiento_orden .modal-header .close {
    position: absolute;
    top: 12px;
    right: 15px;
}

.seguimiento-observacion {
    max-width: 180px;
    white-space: normal;
    font-size: 11px;
    line-height: 1.25;
}

.seguimiento-historial {
    max-height: 190px;
    overflow-y: auto;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 8px;
    background: #f8fafc;
}

.seguimiento-item {
    padding: 7px 8px;
    margin-bottom: 6px;
    border-left: 3px solid #2f80ed;
    background: #ffffff;
    border-radius: 6px;
}
/* Barra de progreso más visible */
.orden-lab-modern #example2 tbody td .progress {
    height: 24px !important;
    min-height: 24px !important;
    margin: 0 0 6px 0 !important;
    border-radius: 8px !important;
    background: #edf2f7 !important;
    box-shadow: inset 0 1px 2px rgba(15, 23, 42, .12) !important;
    overflow: hidden !important;
}

/* Porcentaje centrado dentro de la barra */
.orden-lab-modern #example2 tbody td .progress-bar {
    min-width: 38px !important;
    height: 24px !important;
    line-height: 24px !important;
    border-radius: 8px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    box-shadow: none !important;
}

/* Número del porcentaje dentro de la barra */
.orden-lab-modern #example2 tbody td .progress-bar span {
    display: block !important;
    position: static !important;
    top: auto !important;
    left: auto !important;
    color: #000000 !important;
    font-size: 11px !important;
    font-weight: 800 !important;
    line-height: 24px !important;
    text-align: center !important;
    width: 100% !important;
}

/* Si el porcentaje es 0%, que también se vea el número */
.orden-lab-modern #example2 tbody td .progress-bar[style*="width: 0%"],
.orden-lab-modern #example2 tbody td .progress-bar[style*="width:0%"] {
    min-width: 42px !important;
    background: #f1f5f9 !important;
}

</style>

<!-- Ventana modal devolver orden -->
<div class="modal fade fullscreen-modal" id="modal_eliminar/devolver" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content" id="eliminar/devolver">
      </div>
    </div>
  </div>
<div class="modal fade bs-example-modal-lg" id="modal_datosfacturas" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" id="datos_factura">
            
        </div>
    </div>
</div>
<!--  Modal recibo   --->
<div class="modal fade bs-example-modal-lg" id="modalrecibo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" id="datosrecibo">
            
        </div>
    </div>
</div>
<!-- Ventana Modal Pago -->
<div class="modal fade" id="pago" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content" style="width: 95%;">
      </div>
    </div>  
</div>


<!-- Ventana Modal Reenvio Email -->
<div class="modal fade" id="reenvio_email" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content" style="width: 95%;">
      </div>
    </div>  
</div>


<!-- Ventana Modal Reseteo Clave -->
<div class="modal fade" id="reseteo_clave" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content" style="width: 95%;">
      </div>
    </div>  
</div>

<!-- Ventana modal editar -->
<div class="modal fade fullscreen-modal" id="doctor" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>

<!-- Ventana modal pagoenlinea -->
<div class="modal fade fullscreen-modal" id="pago_online" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>

<!-- Ventana modal gestion -->
<div class="modal fade" id="gestionar_orden" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>

<!-- Ventana modal factura agrupada -->
<div class="modal fade fullscreen-modal" id="modal_agrupada" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content" id="datos_agrupada">
    </div>
  </div>
</div>

<!--EXAMENES PENDIENTES -->
<div class="modal fade bs-example-modal-lg" id="modal_examenes" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" id="div_examenes_pendientes">
            
        </div>
    </div>
</div>

<div class="modal fade" id="log_muestra" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>


<!--EXAMENES QUERY -->
<div class="modal fade bs-example-modal-lg" id="modal_query" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" id="modal_query">

        </div>
    </div>
</div>

<div class="modal fade" id="eliminar_devolver" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    </div>
  </div>
</div>

<div class="modal fade" id="comentarios_orden" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
    </div>
  </div>
</div>

<div class="modal fade bs-example-modal-lg" id="modal_query" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content" id="modal_query">
    </div>
  </div>
</div>

<!-- Main content -->
<section class="content orden-lab-modern">
  <div class="box">
    <div class="box-header">
      <div class="row">
        <div class="col-md-3">
          <h4>{{ trans('orden_ui.laboratory_orders') }}</h4><p class="hidden-xs" style="margin: -2px 0 0 48px; color:#64748b; font-size:12px;">{{ trans('orden_ui.subtitle') }}</p>
        </div>
        <div class="col-md-9">
          <div class="row">
            <div class="col-md-3 col-xs-3" style="margin: 5px;">
              <a style="width: 100%" class="btn btn-warning" href="{{ route('orden.create')}}"><span class="ionicons ion-ios-flask"> </span> {{ trans('orden_ui.public_order_quote') }}</a>
            </div>
            <div class="col-md-3 col-xs-3" style="margin: 5px;">  
              <a style="width: 100%" class="btn btn-primary" href="{{ route('orden_particular.crear_particular')}}"><span class="ionicons ion-ios-flask"> </span> {{ trans('orden_ui.private_order_quote') }}</a>
            </div>
            {{-- <div class="col-md-2 col-xs-3" style="margin: 5px;">
              <a style="width: 100%" id="pago_on" data-toggle="modal" data-target="#pago_online" @if($gestiones->count()>0) class="btn btn-danger" @else class="btn btn-success" @endif  href="{{ route('orden.pagoenlinea_gestionar')}}">
                <span class="glyphicon glyphicon-credit-card"></span> Pagos On Line
              </a>
            </div> --}}
            {{-- <div class="col-md-2 col-xs-3" style="margin: 5px;">
                <a style="width: 100%" class="btn btn-primary" @if(count($consulta)> 0) class="btn btn-danger btn-block btn-xs" @else class="btn btn-info btn-block btn-xs" @endif onclick="ordenesSubir()"><span class="ionicons ion-ios-flask"></span> Ordenes de app</a>
            </div> --}}
            @if($rolUsuario == '1' || Auth::user()->id == '0958064685')
                  <div class="col-md-3 col-xs-3" style="margin: 5px;">
                    <a style="width: 100%" class="btn btn-success" data-remote="{{ route('orden.eliminar_devolver')}}" data-toggle="modal" data-target="#eliminar_devolver"><span class="ionicons ion-ios-flask"> </span> {{ trans('orden_ui.delete_return_order') }} </a>
              </div>
            @endif
          </div>
        </div>
      </div> 
    </div>
    <!-- /.box-header -->
    <div class="box-body">
      <form method="POST" action="{{route('orden.search')}}">
        {{ csrf_field() }}
        <div class="form-row">
          <div class="col-md-12 filtros-principales" style="padding: 0px">
          <div class="form-group col-md-2 col-xs-6">
            <label for="fecha" class="col-md-12 control-label">{{ trans('orden_ui.from') }}</label>
            <div class="col-md-12">
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>
                <input type="text" class="form-control input-sm" name="fecha" id="fecha" autocomplete="off">
                <div class="input-group-addon">
                  <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha').value = '';"></i>
                </div>   
              </div>
            </div>  
          </div>

          <div class="form-group col-md-2 col-xs-6">
            <label for="fecha_hasta" class="col-md-12 control-label">{{ trans('orden_ui.to') }}</label>
            <div class="col-md-12">
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>
                <input type="text" class="form-control input-sm" name="fecha_hasta" id="fecha_hasta" autocomplete="off">
                <div class="input-group-addon">
                  <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_hasta').value = ''; "></i>
                </div>   
              </div>
            </div>  
          </div>

            <div class="form-group col-md-2 col-xs-6">
              <label for="seguro" class="col-md-12 control-label">{{ trans('orden_ui.insurance') }}</label>
              <div class="col-md-12"> 
                <!-- <select id="seguro" name="seguro" class="form-control input-sm" onchange="subnivel();">
                  <option value="">TODOS</option>
                  @foreach ($seguros as $value)
                    <option @if(!is_null($seguro))@if($seguro == $value->id) selected @endif @endif value="{{$value->id}}">{{$value->nombre}}</option>
                  @endforeach
                </select> -->
                <select onchange="subnivel();" class="form-control select2_find_seguro" style="width: 100%;" name="seguro" id="seguro"></select>
              </div>
            </div>

            <div class="form-group col-md-2 col-xs-6">
            <label for="nivel" class="col-md-12 control-label">{{ trans('orden_ui.level') }}</label>
            <div class="col-md-12" id="js_subnivel">

            </div>
          </div>

          <div class="form-group col-md-2 col-xs-6">
            <label for="facturadas" class="col-md-12 control-label">{{ trans('orden_ui.invoiced') }}</label>
            <div class="col-md-12">
              <select id="facturadas" name="facturadas" class="form-control input-sm" onchange="buscar();">
                <option @if($facturadas == "TODAS") selected @endif value="TODAS">{{ trans('orden_ui.all') }}</option>
                <option @if($facturadas == "FACTURADAS") selected @endif value="FACTURADAS">{{ trans('orden_ui.invoiced_plural') }}</option>
                <option @if($facturadas == "NO FACTURADAS") selected @endif value="NO FACTURADAS">{{ trans('orden_ui.not_invoiced') }}</option>
              </select>
            </div>
          </div>
          </div>
          <div class="col-md-12" style="padding: 0px">

            <div class="form-group col-md-4 col-xs-6">
            <label for="nombres" class="col-md-12 control-label">{{ trans('orden_ui.patient') }}</label>
            <div class="col-md-12">
              <div class="input-group">
                <input value="@if($nombres!=''){{$nombres}}@endif" type="text" class="form-control input-sm" name="nombres" id="nombres" placeholder="{{ trans('orden_ui.full_name') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                <div class="input-group-addon">
                  <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('nombres').value = '';"></i>
                </div>
              </div>
            </div>
          </div>

          <div class="form-group col-md-2 col-xs-6">
            <label for="orden" class="col-md-12 control-label">{{ trans('orden_ui.order_number') }}</label>
            <div class="col-md-12">
              <div class="input-group">
                <input type="text" class="form-control input-sm" name="orden" id="orden" >
              </div>
            </div>
          </div>




            <div class="col-md-2 col-xs-12" style="margin: 5px;">
              <button style="margin: 5px;" type="submit" class="btn btn-primary btn-sm" id="boton_buscar">
              <span class="glyphicon glyphicon-search" aria-hidden="true"> {{ trans('orden_ui.search') }}</span>
            </button>
            </div>
          </div>
          <div class="col-md-12" style="padding: 0px">
            {{-- <div class="form-group col-md-2 col-xs-3">
              <button style="margin: 5px; width: 100%" type="submit" class="btn btn-primary btn-sm" formaction="{{route('orden.reporte_index')}}"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Reporte
            </button>
          </div> 
            <div class="form-group col-md-2 col-xs-3">
              <button style="margin: 5px; width: 100%" type="submit" class="btn btn-primary btn-sm" formaction="{{route('orden.reporte_mail')}}"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Envio a mail
            </button>
          </div>      
            <div class="form-group col-md-2 col-xs-3">
              <button style="margin: 5px; width: 100%" type="submit" class="btn btn-success btn-sm" formaction="{{route('orden.reporte_detalle_covid')}}"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Covid
            </button>
            </div> --}}

            <div class="form-group col-md-4 col-xs-4">
              <button style="margin: 5px; width: 100%" type="submit" class="btn btn-danger btn-sm" formaction="{{route('orden_labs.resultados_pendientes')}}"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> {{ trans('orden_ui.pending_exams') }}
              </button>
            </div>

            @php
                  $id_auth    = Auth::user()->id;
            @endphp
            @php 
              $agenda_permiso = Sis_medico\Agenda_Permiso::where('id_usuario', $id_auth)->where('ip_creacion','like','%COM-LABS%')->first();
            @endphp
            @if(!is_null($agenda_permiso) || $rolUsuario =='1')
            <div class="form-group col-md-4 col-xs-4">
              <button style="margin: 5px; width: 100%" type="submit" class="btn btn-primary btn-sm" formaction="{{route('orden.estad_mes')}}"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> {{ trans('orden_ui.statistics') }} </button>
            </div>
            @endif
            {{-- <div class="form-group col-md-4 col-xs-4">
              <a style="margin: 5px; width: 100%" class="btn btn-primary btn-sm" onclick="factura_agrupada();">Factura Agrupada</a>
            </div> --}}
            @if(!is_null($agenda_permiso) || $rolUsuario =='1')
              <div class="form-group col-md-4 col-xs-4">
                <button style="margin: 5px; width: 100%" type="submit" class="btn btn-primary btn-sm" formaction="{{route('orden.reporte_ordenes_dr')}}"><span class="glyphicon glyphicon-file" aria-hidden="true"></span> {{ trans('orden_ui.orders_by_doctor') }}
                </button>
              </div>
            @endif
          </div>
          <div class="col-md-12" style="padding: 0px">
            <div class="form-group col-md-4 col-xs-4">
              <button style="margin: 5px; width: 100%" type="submit" class="btn btn-primary btn-sm" formaction="{{route('orden.reporte_solo_ordenes')}}"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> {{ trans('orden_ui.general_orders') }}
              </button>
            </div>

            {{-- <div class="form-group col-md-2 col-xs-3">
              <button style="margin: 5px; width: 100%" type="submit" class="btn btn-primary btn-sm" formaction="{{route('orden.cotizaciones')}}"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Cotizaciones
              </button>
            </div> --}}

            <div class="form-group col-md-4 col-xs-4">
              <button style="margin: 5px; width: 100%" type="submit" class="btn btn-primary btn-sm" formaction="{{route('orden.reporte_detalle')}}"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> {{ trans('orden_ui.order_details') }}
              </button>
            </div>
            {{-- <div class="form-group col-md-2 col-xs-3">
              <a style="margin: 5px; width: 100%" class="btn btn-success btn-sm" href="{{route('labscomisiones.comisiones')}}">Reporte de Comisiones</a>
            </div> --}}
            <div class="form-group col-md-4 col-xs-4">
              <a style="margin: 5px; width: 100%" class="btn btn-warning btn-sm" href="{{route('c_caja.index')}}">{{ trans('orden_ui.cash_closing') }}</a>
            </div>
          </div>
        </div>        
      </form>
      <div class="col-md-12 pendientes-panel">
        <b>{{ trans('orden_ui.pending_invoicing') }}:</b>@foreach( $nofacturados as $nofacturado) @php $seguro = $seguros->find($nofacturado->id_seguro); @endphp {{$seguro->nombre}}: {{$nofacturado->cantidad}} &nbsp;@endforeach
      </div>
      <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
        <div class="row">
          <div class="table-responsive col-md-12" style="min-height: 210px;">
            <table id="example2" class="table table-bordered table-hover dataTable tabla-ordenes" role="grid" aria-describedby="example2_info" style="font-size: 12px;overflow: none;">
              <thead>
                <tr role="row">
                  <th width="8%">Id <abbr title="{{ trans('orden_ui.priority_help') }}"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></abbr></th>
                  <th width="5%">Fecha Orden</th>
                  <th width="7%" class="col-fecha-creacion">Fecha Creación</th>
                  <th width="7%" class="col-dias-transcurridos">Días transcurridos</th>
                  {{-- <th width="4%">Muestra</th> --}}
                  <th width="18%">{{ trans('orden_ui.names') }}</th>
                  <th width="8%">{{ trans('orden_ui.agreement') }}</th>
                  <th width="5%">{{ trans('orden_ui.status') }}</th>
                  <th width="5%">{{ trans('orden_ui.created_by') }}</th>
                  {{-- <th width="5%">Modificada</th> --}}
                  <th width="5%">{{ trans('orden_ui.quantity') }}</th>
                  <th width="5%">{{ trans('orden_ui.value') }}($)</th>
                  <th width="5%">{{ trans('orden_ui.progress') }}(%)</th>
                  {{-- <th width="10%">Resultados</th>  --}}
                  {{-- <th width="5%">Compro.</th>  --}}
                  {{-- <th width="3%"> <abbr title="Seleccionar si la orden tiene prioridad"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></abbr></th>  --}}
                  <th width="5%">{{ trans('orden_ui.labels') }}</th>
                  <th width="10%">{{ trans('orden_ui.modality') }}</th>
                  <th width="10%">{{ trans('orden_ui.next_visit') }}</th>
                  <th width="8%" class="col-seguimiento">{{ trans('orden_ui.follow_up') }}</th>
                  <th width="10%">{{ trans('orden_ui.observation') }}</th>
                  <th width="8%" class="col-acciones">{{ trans('orden_ui.action') }}</th>
                </tr>
              </thead>
              <tbody>
              @foreach ($ordenes2 as $value) <!--FOR EACH PARA LOS PENDIENTES PUBLICOS ABAJO HAY OTRO-->
                @php

                    $vt_orden = Sis_medico\Examen_Orden::find($value->id);
                    $user = $vt_orden->crea;
                    $fecha_creacion_orden = $vt_orden->created_at;
                    $dias_transcurridos = null;
                    $clase_dias_transcurridos = '';
                    if ($value->estado == '-1' && !is_null($fecha_creacion_orden)) {
                      $fecha_creacion_dia = new \DateTime(date('Y-m-d', strtotime($fecha_creacion_orden)));
                      $fecha_actual_dia = new \DateTime(date('Y-m-d'));
                      $dias_transcurridos = max(0, (int) $fecha_creacion_dia->diff($fecha_actual_dia)->format('%r%a'));
                      $clase_dias_transcurridos = $dias_transcurridos === 0
                        ? 'dias-transcurridos-hoy'
                        : ($dias_transcurridos <= 3
                          ? 'dias-transcurridos-advertencia'
                          : ($dias_transcurridos <= 8 ? 'dias-transcurridos-atencion' : 'dias-transcurridos-urgente'));
                    }
                  @endphp
                  <tr role="row" @if($value->estado =='-1') style="background-color: #FFC9C9;" @elseif($value->estado =='1') style="background-color: #A4F4CF;" @endif>
                    <td>{{$value->id}} / <input id="ch{{ $value->id }}" name="ch{{ $value->id }}" type="checkbox" onclick="act_prioridad({{$value->id}})" @if($value->prioridad_orden == 1) checked @endif @if($value->estado != 1) disabled @endif style="width: 20px; height: 20px;" >
                    </td>
                    <td style="font-size: 11px;">{{substr($value->fecha_orden,0,10)}}</td>
                    <td class="col-fecha-creacion" data-order="{{ !is_null($fecha_creacion_orden) ? strtotime($fecha_creacion_orden) : 0 }}">
                      @if(!is_null($fecha_creacion_orden))
                        {{ date('d/m/Y H:i', strtotime($fecha_creacion_orden)) }}
                      @else
                        -
                      @endif
                    </td>
                    <td class="col-dias-transcurridos" data-order="{{ !is_null($dias_transcurridos) ? $dias_transcurridos : -1 }}">
                      @if(!is_null($dias_transcurridos))
                        <span class="dias-transcurridos-badge {{ $clase_dias_transcurridos }}">
                          {{ $dias_transcurridos === 0 ? 'Hoy · 0 días' : $dias_transcurridos.' '.($dias_transcurridos === 1 ? 'día' : 'días') }}
                        </span>
                      @else
                        -
                      @endif
                    </td>
                    {{-- <td>{{$value->toma_muestra}}@if($vt_orden->toma_muestras->count() > 0)<a data-toggle="modal" data-target="#log_muestra" data-remote="{{route('orden.log_toma_muestras',['id' => $value->id ])}}" class="btn btn-success btn-xs" ><i class="fa fa-plus"></i></a>@endif</td> --}}
                    <td style="font-size: 11px;">{{$value->papellido1}} @if($value->papellido2=='N/A'||$value->papellido2=='(N/A)') @else{{ $value->papellido2 }} @endif {{$value->pnombre1}} @if($value->pnombre2=='N/A'||$value->pnombre2=='(N/A)') @else{{ $value->pnombre2 }} @endif </td>
                    <td style="font-size: 11px;">{{$value->snombre}} / {{$value->nombre_corto}}</td>
                    <td>
                      @if($value->estado=='-1')
                        @if($value->stipo!='0')
                          <span class="label pull-right bg-red" style="font-size: 10px">{{ trans('orden_ui.quoted') }}</span>
                        @else
                          <span class="label pull-right bg-red" style="font-size: 10px">{{ trans('orden_ui.pending') }}</span>
                        @endif
                      @else
                        @if($value->estado_pago =='1')
                          <span class="label pull-right bg-green" style="font-size: 10px">Pag. @if($value->pago_online=='1')Online @endif</span>
                        @endif
                        @if($value->pre_post!=null)
                          <span class="label pull-right bg-primary" style="font-size: 10px">{{$value->pre_post}}</span>
                        @endif
                      @endif
                    </td>
                    <td style="font-size: 11px;@if($user->id_tipo_usuario =='3') color: red; @endif">{{substr($value->cnombre1,0,1)}}. {{$value->capellido1}}</td>
                    {{-- <td style="font-size: 11px;">{{substr($value->mnombre1,0,1)}}. {{$value->mapellido1}}</td> --}}
                    <td>{{$value->cantidad}} {{ trans('orden_ui.exams') }}</td>
                    <td>${{$value->total_valor}}</td>
                    <td>
                      <div class="progress progress">
                        <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" id="td{{$value->id}}">
                          <span id="sp{{$value->id}}" style="color: black;"></span>
                        </div>
                      </div>
                      @if($value->estado=='1')
                        <div class="col-md-12" style="padding-left: 0px">
                          <div class="btn-group btn-documento-uniforme">
                            <button type="button" class="btn btn-success btn-xs btn-documento-principal" onclick="descargar({{$value->id}});"><span style=" font-size: 10px">{{ trans('orden_ui.results') }}</span></button>
                            <button type="button" class="btn btn-success btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="false" style="padding-left: 3px;padding-right: 3px">
                              <span class="caret"></span>
                              <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu" role="menu" style="background-color: #00a65a;padding: 2px;min-width: 80px;">
                              <li ><a  href="{{ route('resultados.imprimir3',['id' => $value->id]) }}" ><span style=" font-size: 10px">Formato Gastro</span></a></li>
                            </ul>
                          </div>
                        </div>
                      @endif
                      @if($value->stipo!='0')
                        <div class="col-md-12" style="padding-left: 0px">
                          <div class="btn-group btn-documento-uniforme">
                              <button type="button" class="btn btn-info btn-xs btn-documento-principal" onclick="window.open('{{ route('cotizador.imprimir', ['id' => $value->id]) }}','_blank');"><span style=" font-size: 9px">COTIZ</span></button>
                              <button type="button" class="btn btn-info btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="false"  style="padding-left: 2px;padding-right: 2px">
                                <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                              </button>
                              <ul class="dropdown-menu cot" role="menu" style="background-color: #00c0ef;padding: 2px;min-width: 80px;">
                                <li ><a  href="{{ route('cotizador.imprimir_gastro', ['id' => $value->id]) }}" target="_blank"><span style=" font-size: 10px">Formato Gastro</span></a></li>
                                <li ><a  href="{{ route('cotizador.imprimir_orden', ['id' => $value->id]) }}" target="_blank"><span style="font-size: 10px">Orden</span></a></li>
                                
                              </ul>
                          </div>
                        </div>
                      @else
                        <div class="col-md-10" style="padding-left: 0px">
                          <input type="hidden" name="_token" value="{{ csrf_token() }}">
                          <a href="{{ route('orden.descargar', ['id' => $value->id]) }}" target="_blank" class="btn btn-block btn-info btn-xs" style="padding: 0px;">
                          <span >Orden</span> 
                          </a>   
                        </div>
                          
                      @endif   
                    </td>
                    {{-- <td>
                    </td> --}}
                    {{-- <td>
                    </td> --}}
		                {{-- <td>
                    </td> --}}
                    <td>
                      @if($value->estado=='1')
                      <a data-toggle="modal" data-target="#modal_query" data-remote="{{route('orden.query.tubo',['id'=> $value->id])}}" class="btn btn-success btn-xs" ><i class="fa fa-plus"></i> Modal</a>
                      @endif
                    </td>
                    <td>
                      @if($value->pres_dom == 1)
                        <span style="font-size: 11px">{{ trans('orden_ui.home_service') }}</span>
                        @else
                        <span style="font-size: 11px">{{ trans('orden_ui.in_person') }}</span>
                      @endif
                    </td>
                    @php
                        $proxima_consulta = DB::table('agenda')
                            ->where('id_paciente', $value->id_paciente)
                            ->where('fechaini', '>=', date('Y-m-d H:i:s'))
                            ->whereIn('proc_consul', [0, 1])
                            ->orderBy('fechaini', 'ASC')
                            ->value('fechaini');
                    @endphp

                    <td>
                        @if(!is_null($proxima_consulta))
                            {{ date('d/m/Y H:i', strtotime($proxima_consulta)) }}
                        @else
                            <span>{{ trans('orden_ui.no_next_visit') }}</span>
                        @endif
                    </td>
                    <td class="col-seguimiento">
                      @if(!is_null($ultimo_seguimiento))
                        <span id="seguimiento_estado_{{$value->id}}" class="seguimiento-badge seguimiento-{{$ultimo_seguimiento->estado_seguimiento}}">
                          {{ isset($seguimiento_labels[$ultimo_seguimiento->estado_seguimiento]) ? $seguimiento_labels[$ultimo_seguimiento->estado_seguimiento] : $ultimo_seguimiento->estado_seguimiento }}
                        </span>
                      @else
                        <span id="seguimiento_estado_{{$value->id}}" class="seguimiento-badge seguimiento-pendiente_llamar">{{ trans('orden_ui.pending_call') }}</span>
                      @endif
                    </td>
                    <td>
                      <div id="seguimiento_obs_{{$value->id}}" class="seguimiento-observacion">
                        @if(!is_null($ultimo_seguimiento))
                          {{ $ultimo_seguimiento->observacion }}
                        @else
                          -
                        @endif
                      </div>
                    </td>
                    <td class="col-acciones">
                      <div class="acciones-orden-stack">
                        <button type="button" class="btn btn-primary btn-xs btn-seguimiento-orden btn-accion-orden" data-id="{{ $value->id }}" data-paciente="{{ e(trim($value->papellido1.' '.$value->papellido2.' '.$value->pnombre1.' '.$value->pnombre2)) }}" data-seguro="{{ e($value->snombre) }}" data-cantidad="{{ e($value->cantidad) }}">
                          <i class="fa fa-phone"></i> {{ trans('orden_ui.follow_up') }}
                        </button>
                        @if($value->stipo!='0')
                          @if($value->realizado=='0' && $value->estado_pago=='0')
                          <a href="{{ route('cotizador.editar', ['id' => $value->id]) }}" class="btn btn-warning btn-xs btn-accion-orden">
                          <!--a href="{{ route('orden.edit2', ['id' => $value->id,'dir' => 'rec']) }}" class="btn btn-block btn-warning btn-xs" style="white-space: normal !important;"-->
                          <span class="glyphicon glyphicon-edit"></span> {{ trans('orden_ui.edit') }}
                          </a>
                          @endif
                        @else
                          @if($value->realizado=='0')
                            <a href="{{ route('orden.edit', ['id' => $value->id]) }}" class="btn btn-warning btn-xs btn-accion-orden">
                            <span class="glyphicon glyphicon-edit"></span> {{ trans('orden_ui.edit') }}
                            </a>
                          @endif
                        @endif
                      </div>
                      {{-- @if($value->realizado=='0' && $value->estado_pago=='0')
                          <div class="col-md-2" style="padding: 3px;">
                            
                      <a href="javascript:eliminar_orden({{$value->id}})" class="btn btn-block btn-danger btn-xs" style="padding: 0px;">
                            <span class="glyphicon glyphicon-trash"></span>
                            </a>  
                          </div>
                      @endif --}}
                      <!--Editar Modal Pago-->
                      {{-- @if($value->stipo!='0')
                        @if($value->estado=='1')
                          @if($value->estado_pago =='0')
                            <div class="col-md-3" style="padding: 1px;">
                              <a data-toggle="modal" data-target="#pago" data-remote="{{route('modal.pago_paciente', ['id_paciente' => $value->id_paciente,'id_exa_orden' => $value->id])}}" class="btn btn-danger btn-xs">Pago
                              </a>
                            </div>
                          @endif
                        @endif
                      @endif --}}

                      {{-- @if(in_array($rolUsuario, array(1)) == true)
                        @if($value->estado_pago =='1')
                        <div class="col-md-3" style="padding: 3px;">
                          <a data-toggle="modal" data-target="#reseteo_clave" data-remote="{{route('paciente_reseteo_clave', ['id_paciente' => $value->id_paciente,'id_exa_orden' => $value->id])}}" class="btn btn-block btn-warning btn-xs" style="padding: 0px;">
                            <span ></span>R
                          </a>  
                        </div>
                        @endif
                      @endif --}}
                      {{-- @if($value->estado_pago =='1')
                        <div class="col-md-3" style="padding: 3px;">
                          <a data-toggle="modal" data-target="#reenvio_email" data-remote="{{route('paciente_reenvio_email', ['id_paciente' => $value->id_paciente,'id_exa_orden' => $value->id])}}" class="btn btn-warning  btn-xs">
                            <span class="glyphicon glyphicon-envelope"></span>
                          </a>
                        </div>
                      @endif --}}
                      {{-- <div class="col-md-2" style="padding: 3px;">
                        <a data-toggle="modal" data-target="#comentarios_orden" data-remote="{{ route('laboratorio.orden.comentarios_ordenes', ['id_orden'=>$value->id]) }}" @if(is_null($value->comentario))  class="btn btn-success btn-xs" @else class="btn btn-danger btn-xs" @endif><i class="fa fa-calendar" aria-hidden="true"></i></a>
                      </div> --}}
                    </td>
                  </tr>
              @endforeach
              @php
                $agrup2 = session()->get('a_orden'); 

              @endphp
              @foreach ($ordenes as $value)
                @php
                 
                 $vt_orden = Sis_medico\Examen_Orden::find($value->id);
                 $user = $vt_orden->crea;
                 $pac = $vt_orden->paciente;
                 $fecha_creacion_orden = $vt_orden->created_at;
                 $dias_transcurridos = null;
                 $clase_dias_transcurridos = '';
                 if ($value->estado == '-1' && !is_null($fecha_creacion_orden)) {
                   $fecha_creacion_dia = new \DateTime(date('Y-m-d', strtotime($fecha_creacion_orden)));
                   $fecha_actual_dia = new \DateTime(date('Y-m-d'));
                   $dias_transcurridos = max(0, (int) $fecha_creacion_dia->diff($fecha_actual_dia)->format('%r%a'));
                   $clase_dias_transcurridos = $dias_transcurridos === 0
                     ? 'dias-transcurridos-hoy'
                     : ($dias_transcurridos <= 3
                       ? 'dias-transcurridos-advertencia'
                       : ($dias_transcurridos <= 8 ? 'dias-transcurridos-atencion' : 'dias-transcurridos-urgente'));
                 }
                 $ultimo_seguimiento = Sis_medico\Examen_Orden_Seguimiento::where('id_examen_orden', $value->id)
                    ->where('estado_registro', '1')
                    ->orderBy('created_at', 'desc')
                    ->first();
                @endphp
                <tr role="row" @if($value->estado =='-1') style="background-color: #FFC9C9;" @elseif($value->estado =='1') style="background-color: #A4F4CF;" @endif>
                  <td>
                    {{-- @if($value->estado == 1)
                      @if($value->fecha_envio == null)
                        @if($value->stipo!='0')
                          @if($value->pago_online == 0)
                            @if($rolUsuario=='5' || $rolUsuario=='1')
                            <a id="btn{{$value->id}}"  class="btn btn-default btn-xs" onclick="guarda_sesion_factura('{{$value->id}}');"><i class="fa fa-plus"></i></a>
                            @endif
                            @if($rolUsuario=='20' || $rolUsuario=='1')
                            <a id="btn_contab{{$value->id}}"  class="btn btn-default btn-xs" onclick="guarda_sesion_factura_contab('{{$value->id}}');" style="background-color: cyan;"><i class="fa fa-plus"></i></a>
                            @endif
                          @endif
                        @else
                          @if($value->realizado == 1)
                            @if($rolUsuario=='20' || $rolUsuario=='1')
                              <a id="btn_contab{{$value->id}}"  class="btn btn-default btn-xs" onclick="guarda_sesion_factura_contab('{{$value->id}}');" style="background-color: cyan;"><i class="fa fa-plus"></i></a>
                            @endif    
                          @endif   
                        @endif
                      @endif
                    @endif --}}
                    {{$value->id}} / <input id="ch{{ $value->id }}" name="ch{{ $value->id }}" type="checkbox" onclick="act_prioridad({{$value->id}})" @if($value->prioridad_orden == 1) checked @endif @if($value->estado != 1) disabled @endif style="width: 20px; height: 20px;" > 
                  </td>
                  <td style="font-size: 11px;">{{substr($value->fecha_orden,0,10)}}</td>
                  <td class="col-fecha-creacion" data-order="{{ !is_null($fecha_creacion_orden) ? strtotime($fecha_creacion_orden) : 0 }}">
                    @if(!is_null($fecha_creacion_orden))
                      {{ date('d/m/Y H:i', strtotime($fecha_creacion_orden)) }}
                    @else
                      -
                    @endif
                  </td>
                  <td class="col-dias-transcurridos" data-order="{{ !is_null($dias_transcurridos) ? $dias_transcurridos : -1 }}">
                    @if(!is_null($dias_transcurridos))
                      <span class="dias-transcurridos-badge {{ $clase_dias_transcurridos }}">
                        {{ $dias_transcurridos === 0 ? 'Hoy · 0 días' : $dias_transcurridos.' '.($dias_transcurridos === 1 ? 'día' : 'días') }}
                      </span>
                    @else
                      -
                    @endif
                  </td>
                  {{-- <td>{{$value->toma_muestra}}@if($vt_orden->toma_muestras->count() > 0)<a data-toggle="modal" data-target="#log_muestra" data-remote="{{route('orden.log_toma_muestras',['id' => $value->id ])}}" class="btn btn-success btn-xs" ><i class="fa fa-plus"></i></a>@endif</td> --}}
                  <td style="font-size: 11px;">{{$value->papellido1}} @if($value->papellido2=='N/A'||$value->papellido2=='(N/A)') @else{{ $value->papellido2 }} @endif {{$value->pnombre1}} @if($value->pnombre2=='N/A'||$value->pnombre2=='(N/A)') @else{{ $value->pnombre2 }} @endif </td>
                  @php $nivel = Sis_medico\Nivel::find($value->id_nivel) @endphp
                  <td style="font-size: 11px;">{{$value->snombre}}-@if(!is_null($nivel)) {{$nivel->nombre}} @endif<br> {{$value->nombre_corto}}</td>
                  <td>
                    @if($value->estado=='-1')
                      @if($value->stipo!='0')
                        <span class="label bg-red" style="font-size: 10px">{{ trans('orden_ui.quoted') }}</span>
                      @else
                        <span class="label bg-red" style="font-size: 10px">{{$value->pre_post}}-{{ trans('orden_ui.pending') }}</span>
                      @endif
                    @else
                      @if($value->estado_pago =='1')
                        <span class="label bg-green" style="font-size: 10px">Pag. @if($value->pago_online=='1')Online @endif</span>
                      @endif
                      @if($value->pre_post!=null)
                        <span class="label bg-primary" style="font-size: 10px">{{$value->pre_post}}</span>
                      @endif
                    @endif
                  </td>
                  <td style="font-size: 11px;@if($user->id_tipo_usuario =='3') color: red !important; @endif">{{substr($value->cnombre1,0,1)}}. {{$value->capellido1}}</td>
                  {{-- <td style="font-size: 11px;">{{substr($value->mnombre1,0,1)}}. {{$value->mapellido1}}</td> --}}
                  <td>{{$value->cantidad}} {{ trans('orden_ui.exams') }}</td>
                  <td>${{$value->total_valor}}</td>
                  <td>
                    <div class="progress progress">
                      <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" id="td{{$value->id}}">
                        <span id="sp{{$value->id}}" style="color: black;"></span>
                      </div>
                    </div>
                    @if($value->estado=='1')
                      <div class="col-md-12" style="padding-left: 0px">
                        <div class="btn-group btn-documento-uniforme">
                          <button type="button" class="btn btn-success btn-xs btn-documento-principal" id="result{{$value->id}}" onclick="descargar({{$value->id}});"><span style=" font-size: 10px">{{ trans('orden_ui.results') }}</span></button>
                          <button type="button" class="btn btn-success btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="false" style="padding-left: 3px;padding-right: 3px">
                            <span class="caret"></span>
                            <span class="sr-only">Toggle Dropdown</span>
                          </button>
                          <ul class="dropdown-menu" role="menu" style="background-color: #00a65a;padding: 2px;min-width: 80px;">
                            <li ><a  href="{{ route('resultados.imprimir3',['id' => $value->id]) }}" ><span style=" font-size: 10px">Formato Gastro</span></a></li>
                            <li ><a  href="javascript:ver_pendientes({{$value->id}})" ><span style=" font-size: 10px">Pendientes</span></a></li>
                          </ul>
                        </div>
                      </div>
                    @endif
                    @if($value->stipo!='0')
                      <div class="col-md-12" style="padding-left: 0px">
                        <div class="btn-group btn-documento-uniforme">
                            <button type="button" class="btn btn-info btn-xs btn-documento-principal"
                            onclick="window.open('{{ route('cotizador.imprimir', ['id' => $value->id]) }}','_blank');"><span style=" font-size: 9px">COTIZ</span></button>
                            <button type="button" class="btn btn-info btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="false"  style="padding-left: 2px;padding-right: 2px">
                              <span class="caret"></span>
                              <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu cot" role="menu" style="background-color: #00c0ef;padding: 2px;min-width: 80px;">
                              <li ><a  href="{{ route('cotizador.imprimir_gastro', ['id' => $value->id]) }}" target="_blank"><span style=" font-size: 10px">Formato Gastro</span></a></li>
                              <li ><a  href="{{ route('cotizador.imprimir_orden', ['id' => $value->id]) }}" target="_blank"><span style=" font-size: 10px">Orden</span></a></li>
                              <li ><a  href="{{ route('pdf_tributario', ['id' => $value->id]) }}" target="_blank"><span style=" font-size: 10px">Comprobante</span></a></li>
                              <li ><a  href="{{ route('pdf_cotizacion', ['id' => $value->id]) }}" target="_blank"><span style=" font-size: 10px">Recibo de Cobro</span></a></li>
                               <li ><a class="hovers" onclick="window.open('{{ route('tiempos.imprimir', ['id' => $value->id]) }}','_blank');" target="_blank"><span style=" font-size: 10px" class="hovers">Tiempos</span></a></li>
                            </ul>
                        </div>
                      </div>
                    @else
                    <div class="col-md-12" style="padding-left: 0px"> 
                        <div class="btn-group" >
                            <button type="button" class="btn btn-info btn-xs"
                            onclick="window.open('{{ route('orden.descargar', ['id' => $value->id]) }}','_blank');"
                            ><span style=" font-size: 9px">Orden</span></button>
                            <button type="button" class="btn btn-info btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="false"  style="padding-left: 2px;padding-right: 2px">
                              <span class="caret"></span>
                              <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu cot" role="menu" style="background-color: #00c0ef;padding: 2px;min-width: 80px;">
                              <li ><a class="hovers" onclick="window.open('{{ route('tiempos.imprimir', ['id' => $value->id]) }}','_blank');" target="_blank"><span style=" font-size: 10px" class="hovers">Tiempos</span></a></li>
                            </ul>
                        </div>
                      </div>
                      <!--<div class="col-md-3" style="padding-left: 0px">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <a href="{{ route('orden.descargar', ['id' => $value->id]) }}" target="_blank" class="btn btn-block btn-info btn-xs" style="padding: 0px;">
                        <span class="glyphicon glyphicon-arrow-down">sdfsdfsd</span>
                        </a>   
                      </div>-->
                      @php 
                        $agendas = Sis_medico\Examen_Orden_Agenda::where('id_orden',$value->id)->get();
                      @endphp 
                      @if($agendas->count()=='1') 
                      <div class="col-md-3" style="padding-left: 0px">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <a href="{{ route('agenda.edit2', ['id' => $agendas->first()->id_agenda, 'doctor' => '4444444444']) }}" target="_blank" class="btn btn-block btn-info btn-xs" style="padding: 0px;">
                        <span class="glyphicon glyphicon-calendar"></span>
                        </a>   
                      </div>
                      @endif
                    @endif
                  </td>
                  {{-- <td>
                  </td> --}}
                  {{-- <td>
                  </td> --}}
		              {{-- <td>
                  </td> --}}
                  
                  <td>
                    @if($value->estado=='1')
                    <a data-toggle="modal" data-target="#modal_query" data-remote="{{route('orden.query.tubo',['id'=> $value->id])}}" class="btn btn-success btn-xs" ><i class="fa fa-plus"></i> Modal</a>
                    @endif
                  </td>
                  <td>
                    @if($value->pres_dom == 1)
                      <span style="font-size: 11px">{{ trans('orden_ui.home_service') }}</span>
                      @else
                      <span style="font-size: 11px">{{ trans('orden_ui.in_person') }}</span>
                    @endif
                  </td>
                  @php
                      $proxima_consulta = DB::table('agenda')
                          ->where('id_paciente', $value->id_paciente)
                          ->where('fechaini', '>=', date('Y-m-d H:i:s'))
                          ->whereIn('proc_consul', [0, 1])
                          ->orderBy('fechaini', 'ASC')
                          ->value('fechaini');
                  @endphp

                  <td>
                      @if(!is_null($proxima_consulta))
                          {{ date('d/m/Y H:i', strtotime($proxima_consulta)) }}
                      @else
                          <span>{{ trans('orden_ui.no_next_visit') }}</span>
                      @endif
                  </td>
                  <td class="col-seguimiento">
                    @if(!is_null($ultimo_seguimiento))
                      <span id="seguimiento_estado_{{$value->id}}" class="seguimiento-badge seguimiento-{{$ultimo_seguimiento->estado_seguimiento}}">
                        {{ isset($seguimiento_labels[$ultimo_seguimiento->estado_seguimiento]) ? $seguimiento_labels[$ultimo_seguimiento->estado_seguimiento] : $ultimo_seguimiento->estado_seguimiento }}
                      </span>
                    @else
                      <span id="seguimiento_estado_{{$value->id}}" class="seguimiento-badge seguimiento-pendiente_llamar">{{ trans('orden_ui.pending_call') }}</span>
                    @endif
                  </td>
                  <td>
                    <div id="seguimiento_obs_{{$value->id}}" class="seguimiento-observacion">
                      @if(!is_null($ultimo_seguimiento))
                        {{ $ultimo_seguimiento->observacion }}
                      @else
                        -
                      @endif
                    </div>
                  </td>
                  <td class="col-acciones">
                    <div class="acciones-orden-stack">
                      <button type="button" class="btn btn-primary btn-xs btn-seguimiento-orden btn-accion-orden" data-id="{{ $value->id }}" data-paciente="{{ e(trim($value->papellido1.' '.$value->papellido2.' '.$value->pnombre1.' '.$value->pnombre2)) }}" data-seguro="{{ e($value->snombre) }}" data-cantidad="{{ e($value->cantidad) }}">
                        <i class="fa fa-phone"></i> {{ trans('orden_ui.follow_up') }}
                      </button>
                      @if($value->stipo!='0')
                        @if($value->realizado=='0' && $value->estado_pago =='0')
                        <a href="{{ route('cotizador.editar', ['id' => $value->id]) }}" class="btn btn-warning btn-xs btn-accion-orden">
                        <!--a href="{{ route('orden.edit2', ['id' => $value->id,'dir' => 'rec']) }}" class="btn btn-block btn-warning btn-xs" style="padding: 0px;"-->
                        <span class="glyphicon glyphicon-edit"></span> {{ trans('orden_ui.edit') }}
                        </a>
                        @endif
                      @else
                        @if($value->realizado=='0')
                          <a href="{{ route('orden.edit', ['id' => $value->id]) }}" class="btn btn-warning btn-xs btn-accion-orden">
                          <span class="glyphicon glyphicon-edit"></span> {{ trans('orden_ui.edit') }}
                          </a>
                        @endif
                      @endif
                    </div>
                    {{-- @if($value->realizado=='0' && $value->estado_pago =='0')
                        <div class="col-md-2" style="padding: 3px;">
                          
                      <a href="javascript:eliminar_orden({{$value->id}})" class="btn btn-block btn-danger btn-xs" style="padding: 0px;">
                          <span class="glyphicon glyphicon-trash"></span>
                          </a>  
                        </div>
                    @endif --}}
                    <!--Editar Modal Pago-->
                    {{-- @if($value->stipo!='0')
                      @if($value->estado=='1')
                        @if($value->estado_pago =='0')
                          <div class="col-md-3" style="padding: 1px;">
                            <a data-toggle="modal" data-target="#pago" data-remote="{{route('modal.pago_paciente', ['id_paciente' => $value->id_paciente,'id_exa_orden' => $value->id])}}" class="btn btn-danger btn-xs">Pago
                            </a>
                          </div>
                        @endif
                      @endif
                    @endif --}}
                    {{-- @if(in_array($rolUsuario, array(1)) == true)
                      @if($value->estado_pago =='1')
                        @if($pac->id == $pac->id_usuario)
                        <div class="col-md-3" style="padding: 3px;">
                          <a data-toggle="modal" data-target="#reseteo_clave" data-remote="{{route('paciente_reseteo_clave', ['id_paciente' => $value->id_paciente,'id_exa_orden' => $value->id])}}" class="btn btn-block btn-warning btn-xs" style="padding: 0px;">
                            <span ></span>R
                          </a>  
                        </div>
                        @endif 
                      @endif
                    @endif --}}
                    {{-- @if($value->estado_pago =='1')
                      <div class="col-md-3" style="padding: 3px;">
                        <a data-toggle="modal" data-target="#reenvio_email" data-remote="{{route('paciente_reenvio_email', ['id_paciente' => $value->id_paciente,'id_exa_orden' => $value->id])}}" class="btn btn-warning  btn-xs">
                          <span class="glyphicon glyphicon-envelope"></span>
                        </a>
                      </div>
                    @endif --}}

                    <div class="acciones-orden-stack">
                      @if($value->stipo!='0')
                        @if($value->estado=='1')
                            @if($value->fecha_envio!=null)
                              <a class="btn btn-primary btn-xs btn-accion-orden" href="{{ route('ventas.comprobante_publico', ['comprobante' => $value->comprobante, 'id_empresa' => $empresa_labs->id, 'tipo' => 'pdf']) }}"><span class="glyphicon glyphicon-file" aria-hidden="true"></span> Ver Factura</a>
                            @else
                            {{-- @if($value->id_seguro == 4 or $value->id_seguro == 33) --}}
                              <button class="btn btn-danger btn-xs btn-accion-orden" onclick="emitir_sri('{{$value->id}}')">SRI</button>
                            {{-- @endif --}}
                            @endif
                        @endif
                      @endif
                    </div>
                    {{-- <div class="col-md-2" style="padding: 3px;">
                      <a data-toggle="modal" data-target="#comentarios_orden" data-remote="{{ route('laboratorio.orden.comentarios_ordenes', ['id_orden'=>$value->id]) }}" @if(is_null($value->comentario))  class="btn btn-success btn-xs" @else class="btn btn-danger btn-xs" @endif><i class="fa fa-comment" aria-hidden="true"></i></a>
                    </div> --}}
                  </td>
                </tr>
              @endforeach
              </tbody>
            </table>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-5">
            <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{1+($ordenes->currentPage()-1)*$ordenes->perPage()}}  / @if(($ordenes->currentPage()*$ordenes->perPage())<$ordenes->total()){{($ordenes->currentPage()*$ordenes->perPage())}} @else {{$ordenes->total()}} @endif de {{$ordenes->total()}} registros</div>
          </div>
          <div class="col-sm-7">
            <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
              {{ $ordenes->appends(Request::only(['fecha', 'fecha_hasta', 'nombres', 'seguro', 'id_nivel', 'facturadas']))->links()}}
            </div>
          </div>
        </div>
      </div>
    </div>
  <!-- /.box-body -->
  </div>
</section>
    <div class="modal fade" id="modal_seguimiento_orden" tabindex="-1" role="dialog" aria-labelledby="modalSeguimientoOrdenLabel">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header btn-primary">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true" style="color:white">&times;</span>
            </button>
            <h4 class="modal-title" id="modalSeguimientoOrdenLabel">{{ trans('orden_ui.order_follow_up') }}</h4>
          </div>
          <div class="modal-body">
            <input type="hidden" id="seguimiento_id_orden">
            <div class="row">
              <div class="col-md-12">
                <p style="margin-bottom: 4px;"><strong>{{ trans('orden_ui.patient') }}:</strong> <span id="seguimiento_paciente"></span></p>
                <p style="margin-bottom: 12px;"><strong>{{ trans('orden_ui.insurance') }}:</strong> <span id="seguimiento_seguro"></span> | <strong>{{ trans('orden_ui.exams') }}:</strong> <span id="seguimiento_cantidad"></span></p>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label>{{ trans('orden_ui.status') }}</label>
                  <select id="seguimiento_estado" class="form-control">
                    <option value="pendiente_llamar">{{ trans('orden_ui.pending_call') }}</option>
                    <option value="contactado">{{ trans('orden_ui.contacted') }}</option>
                    <option value="confirmo_asistencia">{{ trans('orden_ui.attendance_confirmed') }}</option>
                    <option value="no_vendra">{{ trans('orden_ui.will_not_attend') }}</option>
                  </select>
                </div>
              </div>
              <div class="col-md-8">
                <div class="checkbox" style="margin-top: 28px;">
                  <label>
                    <input type="checkbox" id="seguimiento_final" value="1"> {{ trans('orden_ui.follow_up_completed') }}
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="form-group">
                  <label>{{ trans('orden_ui.observation') }}</label>
                  <textarea id="seguimiento_observacion" class="form-control" rows="4" maxlength="1000" placeholder="{{ trans('orden_ui.follow_up_placeholder') }}"></textarea>
                </div>
              </div>
              <div class="col-md-12">
                <label>{{ trans('orden_ui.history') }}</label>
                <div id="seguimiento_historial" class="seguimiento-historial">
                  {{ trans('orden_ui.no_follow_ups') }}
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('orden_ui.close') }}</button>
            <button type="button" class="btn btn-success" onclick="guardarSeguimientoOrden()">
              <i class="fa fa-save"></i> {{ trans('orden_ui.save_follow_up') }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="max-width: 90% !important;">
          <div class="modal-header btn-primary">
            <h5 class="modal-title" id="exampleModalLabel" style="text-align: center;font-weight:bold">Ordenes de la Aplicación</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" >
              <span aria-hidden="true" style="color:white">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="table-responsive">
              <div class="table-responsive-sm">
              <table class="table">
              <thead class="thead-dark" style="text-align:center">
                <tr>
                 <th>Id Orden</th>
                 <th>Paciente</th>
                 <th>Fecha Creación</th>
                 <th>Editar</th>
                </tr>
              </thead>
              <tbody id="filas">
              </tbody>
            </table>
          </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal">cerrar</button>
          </div>
        </div>
      </div>
    </div>
    <!-- /.content -->
<script src="{{ asset('plugins/iCheck/icheck.min.js') }}"></script>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>

<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="{{ asset ('plugins/sweetalert2_6_11/sweetalert2.js') }}"></script>
<script src="{{ asset ('plugins/sweetalert2_6_11/sweetalert2.all.min.js') }}"></script>

<script type="text/javascript">

var seguimientoListarUrl = "{{ route('orden.seguimiento_listar', ['id_orden' => '__ID__']) }}";
var seguimientoStoreUrl = "{{ route('orden.seguimiento_store') }}";
var seguimientoLabels = {
  pendiente_llamar: {!! json_encode(trans('orden_ui.pending_call')) !!},
  contactado: {!! json_encode(trans('orden_ui.contacted')) !!},
  confirmo_asistencia: {!! json_encode(trans('orden_ui.attendance_confirmed')) !!},
  no_vendra: {!! json_encode(trans('orden_ui.will_not_attend')) !!}
};

function seguimientoLabel(estado) {
  return seguimientoLabels[estado] || estado;
}

function seguimientoFecha(fecha) {
  if (!fecha) {
    return '';
  }
  if (typeof fecha === 'object') {
    fecha = fecha.date || fecha.created_at || '';
  }
  fecha = String(fecha);
  var fechaObj = new Date(fecha.replace(' ', 'T'));
  if (isNaN(fechaObj.getTime())) {
    return fecha;
  }
  var dia = ('0' + fechaObj.getDate()).slice(-2);
  var mes = ('0' + (fechaObj.getMonth() + 1)).slice(-2);
  var anio = fechaObj.getFullYear();
  var hora = ('0' + fechaObj.getHours()).slice(-2);
  var minutos = ('0' + fechaObj.getMinutes()).slice(-2);
  return dia + '/' + mes + '/' + anio + ' ' + hora + ':' + minutos;
}

function abrirSeguimientoOrden(idOrden, paciente, seguro, cantidad) {
  $('#seguimiento_id_orden').val(idOrden);
  $('#seguimiento_paciente').text(paciente.replace(/N\/A|\(N\/A\)/g, '').trim());
  $('#seguimiento_seguro').text(seguro);
  $('#seguimiento_cantidad').text(cantidad);
  $('#seguimiento_estado').val('pendiente_llamar');
  $('#seguimiento_observacion').val('');
  $('#seguimiento_final').prop('checked', false);
  $('#seguimiento_historial').html('Cargando historial...');
  $('#modal_seguimiento_orden').modal('show');

  $.ajax({
    type: 'get',
    url: seguimientoListarUrl.replace('__ID__', idOrden),
    datatype: 'json',
    success: function(data) {
      renderSeguimientoHistorial(data.seguimientos || []);
      if (data.seguimientos && data.seguimientos.length > 0) {
        $('#seguimiento_estado').val(data.seguimientos[0].estado_seguimiento);
      }
    },
    error: function() {
      $('#seguimiento_historial').html('<span class="text-danger">No se pudo cargar el historial.</span>');
    }
  });
}

function renderSeguimientoHistorial(seguimientos) {
  if (!seguimientos.length) {
    $('#seguimiento_historial').html('Sin seguimientos registrados.');
    return;
  }

  var html = '';
  $.each(seguimientos, function(index, item) {
    html += '<div class="seguimiento-item">';
    html += '<strong>' + seguimientoLabel(item.estado_seguimiento) + '</strong>';
    html += '<small class="pull-right">' + seguimientoFecha(item.created_at) + '</small>';
    html += '<div style="clear: both;"></div>';
    html += '<div>' + $('<div>').text(item.observacion || '').html() + '</div>';
    html += '<small><strong>Usuario:</strong> ' + (item.usuario || 'N/D') + '</small>';
    if (item.seguimiento_final == 1) {
      html += '<span class="label label-success pull-right">Finalizado</span>';
    }
    html += '</div>';
  });

  $('#seguimiento_historial').html(html);
}

function guardarSeguimientoOrden() {
  var idOrden = $('#seguimiento_id_orden').val();
  var estado = $('#seguimiento_estado').val();
  var observacion = $.trim($('#seguimiento_observacion').val());

  if (observacion == '') {
    Swal.fire('Atención', 'Ingrese una observación para guardar el seguimiento.', 'warning');
    return;
  }

  $.ajax({
    type: 'post',
    url: seguimientoStoreUrl,
    data: {
      _token: "{{ csrf_token() }}",
      id_examen_orden: idOrden,
      estado_seguimiento: estado,
      observacion: observacion,
      seguimiento_final: $('#seguimiento_final').is(':checked') ? 1 : 0
    },
    datatype: 'json',
    success: function(data) {
      if (!data.ok) {
        Swal.fire('Atención', data.mensaje || data.msj || 'No se pudo guardar el seguimiento.', 'warning');
        return;
      }

      $('#seguimiento_estado_' + idOrden)
        .attr('class', 'seguimiento-badge seguimiento-' + estado)
        .text(seguimientoLabel(estado));
      $('#seguimiento_obs_' + idOrden).text(observacion);
      $('#seguimiento_observacion').val('');
      $('#seguimiento_final').prop('checked', false);

      $('#modal_seguimiento_orden').modal('hide');
      Swal.fire('Guardado', 'Seguimiento registrado correctamente.', 'success');
    },
    error: function(data) {
      var mensaje = 'No se pudo guardar el seguimiento.';
      if (data.responseJSON && data.responseJSON.mensaje) {
        mensaje = data.responseJSON.mensaje;
      } else if (data.responseJSON && data.responseJSON.msj) {
        mensaje = data.responseJSON.msj;
      }
      Swal.fire('Error', mensaje, 'error');
    }
  });
}

$(document).on('click', '.btn-seguimiento-orden', function() {
  abrirSeguimientoOrden(
    $(this).data('id'),
    $(this).data('paciente'),
    $(this).data('seguro'),
    $(this).data('cantidad')
  );
});

function ordenesSubir(){
        $.ajax({
          type: 'get',
          url: "{{route('orden.modal_app_orden')}}",
          datatype: 'json',
          success: function(data){
            $('#exampleModal').modal('show');
            let array = data.datos.length;
            $('#filas').empty();
            for (let index = 0; index < array; index++) {
              $('#filas').append(`
                <tr>
                   <td>${data.datos[index]['id']}</td>  
             <td>${data.datos[index]['nombre_paciente']} <br/> <strong>correo:</strong>${data.datos[index]['correo']} <br/> <strong>telefono:</strong> ${data.datos[index]['telefono']} / ${data.datos[index]['telefono1']} / ${data.datos[index]['telefono2']} </td>
             <td>${data.datos[index]['fecha_created_at']} <br/> <strong>Ubicación:</strong>${data.datos[index]['domi']} </td>
                   <td><a class="btn btn-warning btn-xs" onclick="miFunc(${data.datos[index]['id']})">Editar</a></td>
                </tr>`);
            }
          },
          error: function(data){
            alert("Hubi un error");
          }
        }); 
  }

  subnivel();

  $('#eliminar_devolver').on('hidden.bs.modal', function(){
      $(this).removeData('bs.modal');
  });
  
  $('#pago').on('hidden.bs.modal', function(){
      $(this).removeData('bs.modal');
  });

  $('#reenvio_email').on('hidden.bs.modal', function(){
      $(this).removeData('bs.modal');
  });

  $('#reseteo_clave').on('hidden.bs.modal', function(){
      $(this).removeData('bs.modal');
  });

  $('#reseteo_clave').on('hidden.bs.modal', function(){
      $(this).removeData('bs.modal');
  });

  $('#pago_online').on('hidden.bs.modal', function(){
      $(this).removeData('bs.modal');
  });

  $('#gestionar_orden').on('hidden.bs.modal', function(){
      $(this).removeData('bs.modal');
  });

  $('#modal_agrupada').on('hidden.bs.modal', function(){
      $(this).removeData('bs.modal');
  });


  $(document).ready(function($){

    $('#fecha').datetimepicker({
            format: 'YYYY/MM/DD',
            defaultDate: '{{$fecha}}',
            
    });
        
    $('#fecha_hasta').datetimepicker({
            format: 'YYYY/MM/DD',
            defaultDate: '{{$fecha_hasta}}',
            
    });

    @foreach ($ordenes as $value)

      $.ajax({
        type: 'get',
        url:"{{ route('resultados.puede_imprimir',['id' => $value->id]) }}", 
        
        success: function(data){
          
            if(data.cant_par==0){
              var pct = 0;  
            }else{
              var pct = data.certificados/data.cant_par*100;  
            }
            //alert(pct);
            $('#td{{$value->id}}').css("width", Math.round(pct)+"%");
            $('#sp{{$value->id}}').text(Math.round(pct)+"%");
            if(pct < 10){
              $('#td{{$value->id}}').addClass("progress-bar-danger");
              $('#result{{$value->id}}').removeClass("btn-success");
              $('#result{{$value->id}}').addClass("btn-danger");
            }else if(pct >=10 && pct<90){
              $('#td{{$value->id}}').addClass("progress-bar-warning");
              $('#result{{$value->id}}').removeClass("btn-success");
              $('#result{{$value->id}}').addClass("btn-warning");  
            }else{
              $('#td{{$value->id}}').addClass("progress-bar-success");
            }
          

        },


        error: function(data){
          
           
        }
      });

    @endforeach

    
        $("#fecha").on("dp.change", function (e) {
            //buscar();
        });

         $("#fecha_hasta").on("dp.change", function (e) {
            //buscar();
        });
    $(".breadcrumb").append('<li class="active">{{ trans('orden_ui.orders') }}</li>');
    $('#example2').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false,
      'order'       : [[ 1, "desc" ]]
    })



  });

  $('#modal_query').on('hidden.bs.modal', function(){
             location.reload();
             $(this).removeData('bs.modal');
        });

   $('#doctor').on('hidden.bs.modal', function(){
                location.reload();
                $(this).removeData('bs.modal');
            }); 
   $('#modal_datosfacturas').on('hidden.bs.modal', function(){
                location.reload();
                $(this).removeData('bs.modal');
            });
    $('#log_muestra').on('hidden.bs.modal', function(){
                $(this).removeData('bs.modal');
            });         

    $('#comentarios_orden').on('hidden.bs.modal', function(){
                $(this).removeData('bs.modal');
            });

  function buscar()
  {
    var obj = document.getElementById("boton_buscar");
    obj.click();
  }
  function subnivel(){
    var js_input_seguro = $("#seguro").val();
    $.ajax({
            type: 'post',
            url:"{{route('orden.obtener_convenio_seguro')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: { id_seguro: js_input_seguro, @if($id_nivel != null) id_nivel: '{{ $id_nivel }}' @endif },
            success: function(data){
                if(data!='no'){
                    $('#js_subnivel').empty().html(data);
                    //$('#div_nivel').removeClass('oculto');
                    $('#js_subnivel').show();
                }else{
                    //$('#div_nivel').addClass('oculto');
                    $('#js_subnivel').hide();
                    $('#js_subnivel').empty().html('');    
                }         
            },
            error: function(data){
                    
                }
        });

  }
  function descargar(id_or){
    var cert = $('#sp'+id_or).text();
    if(cert=='0%'){
      alert("Sin Exámenes Ingresados");
    }else{
      //location.href = '{{url('resultados/imprimir')}}/'+id_or;
      window.open('{{url('resultados/imprimir')}}/'+id_or,'_blank');  
    }
    
  }
  function emitir_sri(id_orden){
    $.ajax({
      type: 'get',
      url:"{{url('facturacion_labs/info_factura')}}/"+id_orden,
      datatype: 'json',
      success: function(data){
        $('#datos_factura').empty().html(data);
        $('#modal_datosfacturas').modal();
      },
      error: function(data){
        //console.log(data);
      }
    });   
  } 
  function sendInvoice(id_orden){
    $.ajax({
      type: 'get',
      url:"{{url('contable/cierre_caja/recibo/')}}/"+id_orden,
      datatype: 'json',
      success: function(data){
        $('#datosrecibo').empty().html(data);
        $('#modalrecibo').modal();
      },
      error: function(data){
        //console.log(data);
      }
    });  
  }

  function ver_pendientes(id_orden){
    $.ajax({
      type: 'get',
      url:"{{url('examenes_pendientes')}}/"+id_orden,
      datatype: 'json',
      success: function(data){
        
        $('#div_examenes_pendientes').empty().html(data);
        $('#modal_examenes').modal();
      },
      error: function(data){
        //console.log(data);
      }
    });   
  } 

  function factura_agrupada(){
    $.ajax({
      type: 'get',
      url:"{{url('facturacion_labs/modal_factura_agrupada')}}",
      datatype: 'json',
      success: function(data){
        $('#datos_agrupada').empty().html(data);
        $('#modal_agrupada').modal();
      },
      error: function(data){
        //console.log(data);
      }
    }); 

  }

  function eliminar(){
      //alert("hola");
      $.ajax({
            type: 'post',
            url: "{{ url('facturacion_labs/factura_agrup/eliminar_sesion')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
      success: function(data){
          //alert("sucess");
          if(data=='ok'){
            factura_agrupada();
          };
            
      },
      error:  function(){
        alert('error al cargar');
      }
    });
  }

  function guarda_sesion_factura(id_orden){
    $.ajax({
      type: 'get',
      url:"{{url('facturacion_labs/añadir_factura')}}/"+id_orden,
      datatype: 'json',
      success: function(data){
        if(data=='ok'){
          $("#btn"+id_orden).css("background-color", "green"); 
        }else{
          alert("No se puede agregar la orden, seguro o nivel diferente");
        }   
      },
      error: function(data){
        alert("Error no se pudo agregar orden");
      }
    }); 
  }
  
  function guarda_sesion_factura_contab(id_orden){
    $.ajax({
      type: 'get',
      url:"{{url('facturacion_labs/añadir_factura/contabilidad')}}/"+id_orden,
      datatype: 'json',
      success: function(data){
        if(data=='ok'){
          $("#btn_contab"+id_orden).css("background-color", "green"); 
        }  
      },
      error: function(data){
        alert("Error no se pudo agregar orden");
      }
    }); 
  }

  function datos_factura_agrupada(){
      //alert(cuenta);
      $.ajax({
            type: 'get',
            url: "{{ url('facturacion_labs/datos_factura_agrupada')}}",
            datatype: 'json',
      success: function(datahtml){
          //alert("sucess");
          $("#datos_factura_agrup").empty().html(datahtml);
            
      },
      error:  function(){
        alert('error al cargar');
      }
    });
    }

  
  function miFunc(id){
    window.location.href = `{{URL::to('cotizador/editar/${id}')}}`;
  }
  
  
  function eliminar_devolver() {
    $.ajax({
      type: 'get',
      url: "{{url('laboratorio/devolver/orden')}}",
      datatype: 'json',
      success: function(data) {
        //alert("#si");
        $('#eliminar/devolver').empty().html(data);
        $('#modal_eliminar/devolver').modal();
      },
      error: function(data) {
        //console.log(data);
      }
    });
  }	
  
    function eliminar_orden(id) {
      Swal.fire({
      title: 'Esta seguro que desea eliminar la orden ?',
      text: "No podra revertir esta accion!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Si, eliminar!'
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            type: 'get',
            url: "{{url('orden/eliminar')}}/" + id,
            datatype: 'json',
            success: function(data) {
              Swal.fire('Eliminado!','Eliminado correctamente.','success')
              window.location.reload();
            },
            error: function(data) {
              //console.log(data);
            }
          });
        }
      })
    }

  function act_prioridad(id_orden){
    var estado = $("#ch"+id_orden).prop("checked");
    $.ajax({
      type: 'post',
      url:"{{route('orden.prioridad_update')}}",
      headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
      datatype: 'json',
      data: { n_orden: id_orden, estado: estado},
      success: function(data){
        //Swal.fire("Mensaje", "!! Correcto !!", "success");
        //location.reload();
      },
      error: function(data){
        }
    });
  }

  $('.select2_find_seguro').select2({
    placeholder: "Escriba el seguro",
    allowClear: true,
    ajax: {
      url: '{{route("orden.buscar_seguro")}}',
      data: function(params) {
        var query = {
          search: params.term,
          type: 'public'
        }
        return query;
      },
      processResults: function(data) {
        // Transforms the top-level key of the response object from 'items' to 'results'
        console.log(data);
        return {
          results: data
        };
      }
    }
  });

</script>  

@endsection