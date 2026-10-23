@extends('contable.compra.base')
@section('action-content')
@php
use Sis_medico\De_Empresa;
$date = date('Y-m-d');
$h = date('Y-m',strtotime($date));
$deEmpresa = De_Empresa::getDatos($empresa->id);

@endphp
<style type="text/css">
    .dropdown-menu a {
        display: block;
        border-radius: 2;
    }

    .button {
        transition-duration: 0.4s;
    }

    .button:hover {
        background-color: red;
        color: white !important;
    }

    .ui-autocomplete {
        overflow-x: hidden;
        max-height: 200px;
        width: 1px;
        position: absolute;
        top: 100%;
        left: 0;
        z-index: 1000;
        float: left;
        display: none;
        min-width: 460px;
        _width: 460px !important;
        padding: 4px 0;
        margin: 2px 0 0 0;
        list-style: none;
        background-color: #fff;
        border-color: #ccc;
        border-color: rgba(0, 0, 0, 0.2);
        border-style: solid;
        border-width: 1px;
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;
        -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        -webkit-background-clip: padding-box;
        -moz-background-clip: padding;
        background-clip: padding-box;
        *border-right-width: 2px;
        *border-bottom-width: 2px;
    }

    .input-number {
        width: 80%;
        height: 20px;
    }

    .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
    }

    /* Hide default HTML checkbox */
    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    /* The slider */
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    input:checked+.slider {
        background-color: #2196F3;
    }

    input:focus+.slider {
        box-shadow: 0 0 1px #2196F3;
    }

    input:checked+.slider:before {
        -webkit-transform: translateX(26px);
        -ms-transform: translateX(26px);
        transform: translateX(26px);
    }

    /* Rounded sliders */
    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }

    .alerta_correcto {
        position: absolute;
        z-index: 9999;
        top: 100px;
        right: 10px;
    }

    .container {
        display: block;
        position: relative;
        padding-left: 35px;
        margin-bottom: 12px;
        cursor: pointer;
        font-size: 18px;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }

    /* Hide the browser's default checkbox */
    .container input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
        height: 0;
        width: 0;
    }

    /* Create a custom checkbox */
    .checkmark {
        position: absolute;
        top: 0;
        left: 0;
        height: 25px;
        width: 25px;
        background-color: #eee;
    }

    /* .form-control{
            background-color: #f5f5f5;
        }*/

    /* On mouse-over, add a grey background color */
    .container:hover input~.checkmark {
        background-color: #ccc;
    }

    /* When the checkbox is checked, add a blue background */
    .container input:checked~.checkmark {
        background-color: #2196F3;
    }

    /* Create the checkmark/indicator (hidden when not checked) */
    .checkmark:after {
        content: "";
        position: absolute;
        display: none;
    }

    /* Show the checkmark when checked */
    .container input:checked~.checkmark:after {
        display: block;
    }

    /* Style the checkmark/indicator */
    .container .checkmark:after {
        left: 9px;
        top: 5px;
        width: 5px;
        height: 10px;
        border: solid white;
        border-width: 0 3px 3px 0;
        -webkit-transform: rotate(45deg);
        -ms-transform: rotate(45deg);
        transform: rotate(45deg);
    }

    .text {
        color: white;
        padding: 10px;
        background-color: green;
        font-size: 15px;
        font-family: helvetica;
        font-weight: bold;
        text-transform: uppercase;
    }

    .parpadea {

        animation-name: parpadeo;
        animation-duration: 0.5s;
        animation-timing-function: linear;
        animation-iteration-count: infinite;

        -webkit-animation-name: parpadeo;
        -webkit-animation-duration: 4s;
        -webkit-animation-timing-function: linear;
        -webkit-animation-iteration-count: infinite;
    }

    @-moz-keyframes parpadeo {
        0% {
            opacity: 1.0;
        }

        50% {
            opacity: 0.5;
        }

        100% {
            opacity: 1.0;
        }
    }

    @-webkit-keyframes parpadeo {
        0% {
            opacity: 1.0;
        }

        50% {
            opacity: 0.5;
        }

        100% {
            opacity: 1.0;
        }
    }

    @keyframes parpadeo {
        0% {
            opacity: 1.0;
        }

        50% {
            opacity: 0.5;
        }

        100% {
            opacity: 1.0;
        }
    }
</style>

<script type="text/javascript">
    function check(e) {
        tecla = (document.all) ? e.keyCode : e.which;

        //Tecla de retroceso para borrar, siempre la permite
        if (tecla == 8) {
            return true;
        }

        // Patron de entrada, en este caso solo acepta numeros y letras
        patron = /[A-Za-z0-9]/;
        tecla_final = String.fromCharCode(tecla);
        return patron.test(tecla_final);
    }

    function goBack() {
        location.href = "{{ route('compras_index') }}";
    }
</script>
<input type="hidden" id="fechita" value="{{$date}}">
<link rel="stylesheet" href="{{ asset('/css/icheck/all.css')}}">
<link rel="stylesheet" href="{{ asset('/css/bootstrap-datetimepicker.css')}}">
<div class="modal fade bd-example-modal-lg" id="calendarModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" id="content">
        </div>
    </div>
</div>
<div class="modal fade" id="modalpedido" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalDoctor" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>
<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Tributación</a></li>
            <li class="breadcrumb-item active" aria-current="page">Estado documentos SRI</li>
        </ol>
    </nav>
    <form class="form-vertical " id="crear_factura" role="form" method="post">
        {{ csrf_field() }}
        <div class="box box-solid">
            <div class="box-header header_new">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-6 col-sm-9 col-6">
                            <div class="box-title"><b>Gestionar documentos</b></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-body dobra">
                <div class="header row">
                    <div class="col-md-12 table-responsive" style="width: 100%;">
                        <table id="tablaDocumentos" class="table table-hover dataTable" width="100%" role="grid" aria-describedby="example2_info">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>...</th>
                                    <th>Estado</th>
                                    <th>Tipo</th>
                                    <th>Fecha</th>
                                    <th>Comprobante</th>
                                    <th>RUC</th>
                                    <th>Nombre</th>
                                    <th>No. autorización</th>
                                    <th>Clave Acceso</th>
                                </tr>
                            </thead>
                            <tbody id="cuerpo">
                                <?php
                                if (isset($documentos) && count($documentos) > 0) {
                                    $tabla = '';
                                    $count = 0;
                                    $tipoDoc = '';
                                    foreach ($documentos as $row) {
                                        $count++;
                                        if ($row->documento == 1) {
                                            $tipoDoc = 'Factura';
                                            $tabla = 'ct_ventas';
                                        } elseif ($row->documento == 3) {
                                            $tipoDoc = 'Liquidación';
                                            $tabla = 'ct_compras';
                                        } elseif ($row->documento == 4) {
                                            $tipoDoc = 'Nota de crédito';
                                            $tabla = 'ct_nota_credito_clientes';
                                        } elseif ($row->documento == 5) {
                                            $tipoDoc = 'Nota de débito';
                                            $tabla = 'ct_nota_debito_clientes';
                                        } elseif ($row->documento == 6) {
                                            $tipoDoc = 'Guía de remisión';
                                            $tabla = 'ct_cabecera_remision';
                                        } elseif ($row->documento == 7) {
                                            $tipoDoc = 'Retención';
                                            $tabla = 'ct_retenciones';
                                        }
                                        echo '<tr>                                      
                                        <td>' . $count . '</td>
                                        <td>
                                        <div class="dropdown">
                                        <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                          <i class="glyphicon glyphicon-retweet"></i>
                                          <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                                          <li><a href="javascript:;" onclick="pdf_ride(\'' . $row->nro_comprobante . '\');"><i class="fa fa-file-pdf-o"></i> PDF</a></li>
                                          <li><a href="#"><i class="fa fa-file-code-o"></i> XML</a></li>
                                          <li><a href="#" onclick="verificarDocumento(\'' . $row->nro_autorizacion . '\');"><i class="fa fa-check-circle"></i> Verificar autorización</a></li>
                                          <li role="separator" class="divider"></li>
                                          <li><a href="javascript:;" onclick="verComprobanteAntes(\'' . $row->id . '\',\'' . $tabla . '\');"><i class="fa fa-eye"></i> Ver comprobante antes de enviar</a></li>
                                          <li><a href="javascript:;"><i class="fa fa-envelope-o"></i> Reenviar email</a></li>
                                          <li role="separator" class="divider"></li>
                                          <li><a href="javascript:;" onclick="logDocumento(\'' . $row->id . '\',\'' . $tabla . '\')"><i class="fa fa-list"></i> Log</a></li>
                                          <li role="separator" class="divider"></li>
                                          <li><a href="javascript:;" onclick="reprocesarDocumento(\'' . $row->id . '\',\'' . $tabla . '\');"><i class="fa fa-repeat"></i> Reprocesar</a></li>
                                        </ul>
                                      </div>
                                        </td>
                                        <td>' . $row->estado . '</td>
                                        <td>' . $tipoDoc . '</td>
                                        <td>' . date('Y-m-d', strtotime($row->fecha)) . '</td>
                                        <td>' . $row->nro_comprobante . '</td>
                                        <td>' . $row->ruc . '</td>
                                        <td>' . $row->nombre . '</td>
                                        <td>' . $row->nro_autorizacion . '</td>
                                        <td>' . $row->clave_acceso . '</td>
                                        </tr>';
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
    </form>
</section>
<div class="modal fade bd-example-modal-lg" id="myModal" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background:#f6b33d -moz-linear-gradient(center top , #f6b33d 5%, #d29105 100%) repeat scroll 0 0;">
                <a class="close" data-dismiss="modal">×</a>
                <h3 id="titulo"></h3>
            </div>
            <div class="modal-body" id="body">
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ('/js/icheck.js') }}"></script>
<script src="{{ asset ('/js/jquery-ui.js')}}"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#tablaDocumentos').DataTable({
            dom: '<"html5buttons"B>lTfrtip',//lBrtip //lTfrtip
            responsive: true,
            pageLength: 100,
            buttons: [{
                extend: 'excelHtml5',
                footer: true,
                title: 'Estado de documentos electrónicos'
            }],

            /*dom: '<"html5buttons"B>lTfrtip',
            buttons: [{
                    extend: 'copy',
                    text: '<i class="fa fa-copy"></i> Copy'
                },
                {
                    extend: 'csv',
                    text: '<i class="fa fa-file-code"></i> CSV'
                },
                {
                    extend: 'excel',
                    title: 'ExampleFile',
                    text: '<i class="fa fa-file-excel"></i> Excel'
                },
                {
                    extend: 'pdf',
                    title: 'ExampleFile',
                    text: '<i class="fa fa-file-pdf"></i> PDF'
                }, {
                    extend: 'print',
                    text: '<i class="fa fa-print"></i> Print',
                    customize: function(win) {
                        $(win.document.body).addClass('white-bg');
                        $(win.document.body).css('font-size', '10px');
                        $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                    }
                }
            ],*/
        });
    });
    window.onload = function() {
        document.querySelector(".sidebar-toggle").click();
    }

    function verificarDocumento(clave) {
        $.ajax({
            url: 'verificarDocumento',
            type: 'post',
            data: {
                _token: $('input[name=_token]').val(),
                clave: clave
            },
            dataType: 'html',
            success: function(html) {
                $('#body').html(html);
                $('#titulo').html('Verificación de documentos electrónicos');
                $('#myModal').modal('toggle');
            }
        });

    }

    function logDocumento(id, tabla) {
        $.ajax({
            url: 'logDocumento',
            type: 'post',
            data: {
                _token: $('input[name=_token]').val(),
                id: id,
                tabla: tabla
            },
            dataType: 'html',
            success: function(html) {
                $('#body').html(html);
                $('#titulo').html('Log del documento');
                $('#myModal').modal('toggle');
            }
        });
    }

    function verComprobanteAntes(id, tabla) {
        $.ajax({
            url: 'verComprobanteAntes',
            type: 'post',
            data: {
                _token: $('input[name=_token]').val(),
                id: id,
                tabla: tabla
            },
            dataType: 'html',
            success: function(html) {
                $('#body').html(html);
                $('#titulo').html('Comprobante antes de enviar al SRI');
                $('#myModal').modal('toggle');
            }
        });
    }

    function reprocesarDocumento(id, tabla) {
        $.ajax({
            url: 'reprocesarDocumento',
            type: 'post',
            data: {
                _token: $('input[name=_token]').val(),
                id: id,
                tabla: tabla
            },
            dataType: 'html',
            success: function(html) {
                $('#body').html(html);
                $('#titulo').html('Reproceso del documento');
                $('#myModal').modal('toggle');
            }
        });
    }

    function pdf_ride(num) {
        var c1 = 0,
            c2 = 0,
            c3 = 0;
        num = num.split('-');
        c1 = num[0];
        c2 = num[1];
        c3 = num[2];
        var ruc_labs = $('#ruc_labs').val();
        console.log(c3);
        if (c1 == 0) {
            alert('No existe RIDE.')
        } else {
            var url = "{{url('/contable/ventas/ride_pdf/')}}/" + c1 + '-' + c2 + '-' + c3.toString().padStart(9, 0) + '/<?= session('id_empresa') ?>/' + 'pdf';
            window.open(url, '_blank');
        }
    }
</script>
@endsection