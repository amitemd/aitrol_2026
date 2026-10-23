@extends('contable.libro_mayor.base')
@section('action-content')
<style>
    p.s1 {
        margin-left: 10px;
        font-size: 14px;
        font-weight: bold;
    }

    p.s2 {
        margin-left: 20px;
        font-size: 12px;
        font-weight: bold;
    }

    p.s3 {
        margin-left: 30px;
        font-size: 10px;
        font-weight: bold;
    }

    p.s4 {
        margin-left: 40px;
        font-size: 10px;
    }

    p.t1 {
        font-size: 14px;
        font-weight: bold;
    }

    p.t2 {
        font-size: 12px;
        font-weight: bold;
    }

    p.t3 {
        font-size: 10px;
    }

    .modal_asiento:hover {
        cursor: pointer;
    }

    .table-condensed>thead>tr>th>td,
    .table-condensed>tbody>tr>th>td,
    .table-condensed>tfoot>tr>th>td,
    .table-condensed>thead>tr>td,
    .table-condensed>tbody>tr>td,
    .table-condensed>tfoot>tr>td {
        padding: 1 px;
        line-height: 1;
    }
</style>
<link rel="stylesheet" href="{{ asset('/css/bootstrap-datetimepicker.css')}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<div class="modal fade bs-example-modal-lg" id="modal_datosfacturas" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" id="datos_factura">
        </div>
    </div>
</div>
<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">{{trans('contableM.contable')}}</a></li>
            <li class="breadcrumb-item"><a href="#">{{trans('contableM.Contabilidad')}}</a></li>
            <li class="breadcrumb-item"><a href="../">{{trans('contableM.libromayor')}}</a></li>
        </ol>
    </nav>
    <div class="box" style=" background-color: white;">
        <div class="row head-title">
            <div class="col-md-12 cabecera">
                <label class="color_texto" for="title">{{trans('contableM.Buscador')}}</label>
            </div>
        </div>
        <div class="box-body dobra">
            <form method="POST" id="reporte_master" action="{{ route('libromayor.index') }}">
                {{ csrf_field() }}
                <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
                    <label for="fecha" class="texto col-md-4 control-label">{{trans('contableM.FechaDesde')}}</label>
                    <div class="col-md-9">
                        <div class="input-group date">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" class="form-control input-sm" name="fecha" id="fecha" value="<?= isset($desde) ? date('d/m/Y', strtotime($desde)) : ''; ?>" required autocomplete="off">
                            <div class="input-group-addon">
                                <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha').value = ''; buscar();"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
                    <label for="fecha_hasta" class="texto col-md-4 control-label">{{trans('contableM.Fechahasta')}}</label>
                    <div class="col-md-9">
                        <div class="input-group date">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" class="form-control input-sm" name="fecha_hasta" id="fecha_hasta" value="<?= isset($hasta) ? date('d/m/Y', strtotime($hasta)) : ''; ?>" required autocomplete="off">
                            <div class="input-group-addon">
                                <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_hasta').value = ''; buscar();"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
                    <label for="fecha" class="texto col-md-4 control-label">Cuentas:</label>
                    <div class="col-md-9">
                        <select id="cuenta" name="cuenta" class="form-control select2_cuentas" style="width: 100%;" required>
                            <option value="">--Seleccionar--</option>
                            <?php
                            if (isset($cuentas) && count($cuentas) > 0) {
                                foreach ($cuentas as $row) {
                                    $select = '';
                                    if (isset($cuenta)) {
                                        if ($row->plan == $cuenta)
                                            $select = 'selected';
                                    }
                                    echo '<option value="' . $row->plan . '" ' . $select . '>' . $row->plan . ' | ' . $row->nombre . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group col-md-6 col-xs-9 pull-right" style="text-align: right;">
                    <button type="submit" class="btn btn-primary" id="boton_buscar">
                        <span class="glyphicon glyphicon-search" aria-hidden="true"></span> {{trans('contableM.buscar')}}
                    </button>
                </div>
            </form>
            <div class="col-md-12">
                <?php
                $debe = 0;
                $haber = 0;
                if (!isset($mayor)) {
                ?>
                    <center style="font-size: 25px;font-weight: 500;">
                        @lang('contableM.noencontrresul')
                    </center>
                <?php
                } else {
                ?>
                    <div id="imprimir_libro" class="row">
                        <div class="table-responsive col-md-12">
                            <table id="tablaMayor" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Asiento</th>
                                        <th>Año</th>
                                        <th>Mes</th>
                                        <th>Información</th>
                                        <th>Cuenta</th>
                                        <th>Nombre de la cuenta</th>
                                        <th>Detalle</th>
                                        <th>Identificación</th>
                                        <th>Socio</th>
                                        <th>Debe</th>
                                        <th>Haber</th>
                                        <th>Saldo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (count($mayor) > 0) {
                                        $saldo = 0;
                                        /*echo '<pre>';
                                        print_r($mayor);
                                        exit;*/
                                        foreach ($mayor as $row) {
                                            $saldo += round($row->debe, 2, PHP_ROUND_HALF_DOWN) - round($row->haber, 2, PHP_ROUND_HALF_DOWN);
                                            $debe += $row->debe;
                                            $haber += $row->haber;
                                            $mes = ["enero", "febrero", "marzo", "abril", "mayo", "junio", "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre"][date('n', strtotime($row->fecha_asiento)) - 1];
                                            echo '<tr>
                                            <td>' . date('Y-m-d', strtotime($row->fecha_asiento)) . '</td>
                                            <td><a href="' . url('/') . '/contable/contabilidad/libro/edit/' . $row->id_asiento_cabecera . '" target="_new">' . $row->id_asiento_cabecera . '</a></td>
                                            <td>' . date('Y', strtotime($row->fecha_asiento)) . '</td>
                                            <td>' . strtoupper($mes) . '</td>
                                            <td>' . $row->informacion . '</td>
                                            <td>' . $row->plan . '</td>
                                            <td>' . $row->nombre . '</td>
                                            <td>' . $row->observacion . '</td>
                                            <td>' . $row->identificacion . '</td>
                                            <td>' . $row->socio . '</td>
                                            <td>' . $row->debe . '</td>
                                            <td>' . $row->haber . '</td>
                                            <td>' . number_format($saldo, 2) . '</td>
                                            </tr>';
                                        }
                                    }
                                    ?>
                                </tbody>
                                <tfoot>
                                    <th>&nbsp;</th>
                                    <th>&nbsp;</th>
                                    <th>&nbsp;</th>
                                    <th>&nbsp;</th>
                                    <th>&nbsp;</th>
                                    <th>&nbsp;</th>
                                    <th>&nbsp;</th>
                                    <th>&nbsp;</th>
                                    <th>Totales</th>
                                    <th>&nbsp;</th>
                                    <th><?= $debe; ?></th>
                                    <th><?= $haber; ?></th>
                                    <th>&nbsp;</th>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>
</section>
<script src="{{ asset ('/plugins/datetimepicker/bootstrap-material-datetimepicker.js') }}"></script>
<script src="{{ asset ('/js/bootstrap-datetimepicker.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>

<script src="{{ asset ('/js/jquery-ui.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
        $('#tablaMayor').DataTable({
            paging: true,
            //dom: 'QlBrtip',
            dom: '<"html5buttons"B>lTfgitp',
            ordering: true,
            responsive: false,
            searching: true,
            info: true,
            pageLength: 1000,
            autoWidth: true,
            buttons: [{
                    extend: 'copyHtml5',
                    footer: true
                },
                {
                    extend: 'excelHtml5',
                    footer: true,
                    title: $('select[name="cuenta"] option:selected').text(),
                },
                {
                    extend: 'csvHtml5',
                    footer: true
                },
                {
                    extend: 'pdfHtml5',
                    orientation: 'landscape',
                    pageSize: 'TABLOID',
                    footer: true,
                    title: $('select[name="cuenta"] option:selected').text(),

                    customize: function(doc) {
                        doc.styles.title = {
                            color: 'black',
                            fontSize: '16',
                            alignment: 'center'
                        }
                    }
                }
            ],
            /*dom: '<"html5buttons"B>lTfgitp',
            buttons: [{
                    extend: 'copy',
                },
                {
                    extend: 'csv',
                },
                {
                    extend: 'excel',
                    title: 'ExampleFile',
                },
                {
                    extend: 'pdf',
                    title: 'ExampleFile',
                }, {
                    extend: 'print',
                    customize: function(win) {
                        $(win.document.body).addClass('white-bg');
                        $(win.document.body).css('font-size', '10px');
                        $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                    }
                }
            ],
            responsive: true,
            displayLengtn: 100,
            bPaginate: true,*/
        });
        $('.select2_cuentas').select2({
            tags: false
        });
    });

    $('#seguimiento').on('hidden.bs.modal', function() {
        $(this).removeData('bs.modal');
    });
    $('#cuenta').select2().trigger('change');
    $('#nombre').select2().trigger('change');
    $('#cuenta_hasta').select2().trigger('change');
    $('#nombre_hasta').select2().trigger('change');
    $('#cuenta').on('select2:select', function(e) {
        var cuenta = $('#cuenta').val();
        $('#nombre').val(cuenta);
        $('#nombre').select2().trigger('change');
    });
    $('#nombre').on('select2:select', function(e) {
        var nombre = $('#nombre').val();
        $('#cuenta').val(nombre);
        $('#cuenta').select2().trigger('change');
    });
    $('#cuenta_hasta').on('select2:select', function(e) {
        var cuenta_hasta = $('#cuenta_hasta').val();
        $('#nombre_hasta').val(cuenta_hasta);
        $('#nombre_hasta').select2().trigger('change');
    });
    $('#nombre_hasta').on('select2:select', function(e) {
        var nombre_hasta = $('#nombre_hasta').val();
        $('#cuenta_hasta').val(nombre_hasta);
        $('#cuenta_hasta').select2().trigger('change');
    });
    $(function() {
        var selectedValues = new Array();
        selectedValues[0] = "1";
        selectedValues[1] = "1.01.01.1";
        $('#fecha').datetimepicker({
            format: 'DD/MM/YYYY',
        });
        $('#fecha_hasta').datetimepicker({
            format: 'DD/MM/YYYY',
        });
        $("#fecha").on("dp.change", function(e) {
            verifica_fechas();
        });
        $("#fecha_hasta").on("dp.change", function(e) {
            verifica_fechas();
        });
    });

    function buscar() {
        var obj = document.getElementById("boton_buscar");
        obj.click();
    }

    function switAlert(icon, title, text) {
        Swal.fire({
            icon: `${icon}`,
            title: `${title}`,
            text: `${text}`
        });
    }

    function verifica_fechas() {
        if (Date.parse($("#fecha").val()) > Date.parse($("#fecha_hasta").val())) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: `{{trans('winsumos.verifiquerangofechayveuvlaconsultar')}}`
            });
        }
    }


    function validar() {
        let fecha = document.getElementById('fecha').value;
        let fecha_hasta = document.getElementById('fecha_hasta').value;
        let cuenta = document.getElementById('cuenta').value;
        if (fecha == "") {
            switAlert("error", "Opss......", "trans('contableM.debeselecfecha')");
        } else if (fecha_hasta == "") {
            switAlert("error", "Opss......", "trans('contableM.debeselecfechahasta')");
        } else if (cuenta == "") {
            switAlert("error", "Opss......", "trans('contableM.debeselecunacuenta')");
        } else {
            imprimirElemento();
        }
    }

    function imprimirElemento() {
        var ventana = window.open('', 'PRINT');
        var elemento = document.getElementById('example2_wrapper').innerHTML;
        ventana.document.write('<html><head><title>' + document.title + '</title>');
        ventana.document.write(estilos()); //Aquí agregué la hoja de estilos
        ventana.document.write('</head><body >');
        ventana.document.write(elemento);
        ventana.document.write('</body></html>');
        ventana.document.close();
        ventana.focus();
        ventana.onload = function() {
            ventana.print();
            ventana.close();
        };
        return true;
    }

    function showmodal(id_orden) {
        $.ajax({
            type: 'get',
            url: "{{url('contable/getmodal/type')}}/" + id_orden,
            datatype: 'json',
            success: function(data) {
                $('#datos_factura').empty().html(data);
                $('#modal_datosfacturas').modal();
            },
            error: function(data) {
                //console.log(data);
            }
        });
    }

    function estilos() {
        let estilos_tablas = `
      <style>
      .well-dark {
        background-color: #aaa;
        width: 100%;
        color: #fafafa;
      }
      th{
        text-align: start;
      }
      .box-body{
        display: flex!important;
      }
      dl{
        margin-top: 39px;
        margin-right: 38px;
      }
      .col-md-4 h4, h5{
        margin: 0;
      }
      .col-md-4{
          margin-top: 35px;
      }

      </style>`;
        return estilos_tablas;
    }
</script>
@endsection