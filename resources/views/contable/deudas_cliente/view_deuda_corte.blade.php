@extends('contable.balance_comprobacion.base')
@section('action-content')
<style>
    p.s1 {
        margin-left: 15px;
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

    .table-striped>thead>tr>th>td,
    .table-striped>tbody>tr>th>td,
    .table-striped>tfoot>tr>th>td,
    .table-striped>thead>tr>td,
    .table-striped>tbody>tr>td,
    .table-striped>tfoot>tr>td {
        padding: 0.70px;
        text-align: center;
    }

    .secundario {
        left: 10px;
    }

    .table {
        margin-bottom: -10px;
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
        min-width: 160px;
        _width: 160px;
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

    .hidden-paginator {

        display: none;

    }

    .removethe {
        display: none;
    }
</style>
<link rel="stylesheet" href="{{ asset('/css/bootstrap-datetimepicker.css')}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<link rel="stylesheet" href="{{ asset('/css/icheck/all.css')}}">
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.2/css/jquery.dataTables.min.css">
<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Contable</a></li>
            <li class="breadcrumb-item"><a href="#">Contabilidad</a></li>
            <li class="breadcrumb-item"><a href="#">Corte a Fecha</a></li>
        </ol>
    </nav>
    <div class="box" style=" background-color: white;">
        <div class="row head-title">
            <div class="col-md-12 cabecera">
                <label class="color_texto" for="title">BUSCADOR</label>
            </div>
        </div>
        <div class="box-body dobra">
            <form method="POST" id="reporte_master" action="{{ route('cortefecha_') }}">
                {{ csrf_field() }}
                <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
                    <label for="fecha" class="texto col-md-3 control-label">*Fecha desde:</label>
                    <div class="col-md-9">
                        <div class="input-group date">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" class="form-control input-sm" name="fecha_desde" id="fecha_desde" autocomplete="off">
                            <div class="input-group-addon">
                                <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_desde').value = ''; buscar();"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
                    <label for="fecha_hasta" class="texto col-md-3 control-label">Fecha hasta:</label>
                    <div class="col-md-9">
                        <div class="input-group date">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" class="form-control input-sm" name="fecha_hasta" id="fecha_hasta" autocomplete="off">
                            <div class="input-group-addon">
                                <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_hasta').value = ''; buscar();"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group col-md-6 col-xs-9 pull-right" style="text-align: right;">
                    <button type="submit" class="btn btn-primary btn-gray" id="btn_corte">
                        <span class="fa fa-scissors" aria-hidden="true"></span> Corte a fecha
                    </button>
                    <button type="button" class="btn btn-primary btn-gray" onclick="excel();" id="btn_exportar">
                        <span class="glyphicon glyphicon-save-file" aria-hidden="true"></span> Exportar
                    </button>
                </div>
                <div class="form-group col-md-6 col-xs-9" style="text-align: right;">

                </div>
            </form>
        </div>
        <form method="POST" id="print_reporte_master" action="{{ route('deudasvspagosc.excel') }}" target="_blank">
            {{ csrf_field() }}
            <input type="hidden" name="filfecha_desde" id="filfecha_desde" value="{{$fecha_desde}}">
            <input type="hidden" name="filfecha_hasta" id="filfecha_hasta" value="{{$fecha_hasta}}">
            <input type="hidden" name="es_fact_dos" id="es_fact_dos">
        </form>
        @if(isset($dato) && count($dato)>0)
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-solid">
                        <div class="box-header with-border">
                        </div>
                        <div class="box-body">
                            <div class="col-md-1">
                                <dl>
                                    <dd><img src="{{asset('/logo').'/'.$empresa->logo}}" alt="Logo Image" style="width:80px;height:80px;" id="logo_empresa"></dd>
                                </dl>
                            </div>
                            <div class="col-md-3">
                                <dl>
                                    <dd><strong>{{$empresa->nombrecomercial}}</strong></dd>
                                    <dd>&nbsp; {{$empresa->id}}</dd>
                                </dl>
                            </div>
                            <div class="col-md-4">
                                <h4 style="text-align: center;">DEUDAS VS PAGOS</h4>
                                @if(($fecha_desde!=null))
                                <h5 style="text-align: center;">Desde {{date("d-m-Y", strtotime($fecha_desde))}} - Hasta {{date("d-m-Y", strtotime($fecha_hasta))}}</h5>
                                @else
                                <h5 style="text-align: center;">Al - {{date("d-m-Y", strtotime($fecha_hasta))}}</h5>
                                @endif
                            </div>
                            <div class="col-md-4">
                            </div>
                            <div class="col-md-12">
                                <table id="example2" class="display compact" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                                    <thead>
                                        <tr>
                                            <th width="30%" style="text-align:center;" tabindex="0" aria-controls="example2">Detalle</th>
                                            <th width="10%" style="text-align:center;" tabindex="0" aria-controls="example2">Fecha</th>
                                            <th width="20%" style="text-align:center;" tabindex="0" aria-controls="example2"> </th>
                                            <th width="10%" style="text-align:right" tabindex="0" aria-controls="example2">Valor</th>
                                            <th width="10%" style="text-align:right" tabindex="0" aria-controls="example2">Debe</th>
                                            <th width="10%" style="text-align:right" tabindex="0" aria-controls="example2">Haber</th>
                                            <th width="10%" style="text-align:right" tabindex="0" aria-controls="example2">Saldo</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot>
                                        <tr>
                                            <td><label>TOTAL :</label></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td style="text-align:right; font-weight: bold;">@if(isset($debe)){{number_format($debe,2,'.','')}}@endif</td>
                                            <td style="text-align:right; font-weight: bold;">@if(isset($haber)){{number_format($haber,2,'.','')}}@endif</td>
                                            <td style="text-align:right; font-weight: bold;"> @if(isset($totales)){{number_format($totales,2,'.','')}}@endif</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</section>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ('/plugins/datetimepicker/bootstrap-material-datetimepicker.js') }}"></script>
<script src="{{ asset ('/js/bootstrap-datetimepicker.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script src="{{ asset ('/js/icheck.js') }}"></script>
<script src="{{ asset ('/js/jquery-ui.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {

        $('input[type="checkbox"].flat-green').iCheck({
            checkboxClass: 'icheckbox_flat-green',
            radioClass: 'iradio_flat-green'
        });
        $('input[type="checkbox"].flat-red').iCheck({
            checkboxClass: 'icheckbox_flat-red',
            radioClass: 'iradio_flat-red'
        });
        $('#btn_corte').click(function() {
            if ($('#fecha_desde').val() == '' || $('#fecha_hasta').val() == '') {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Verifique el rango de fechas y vuelva consultar'
                });
            } else {
                window.open('<?= url('/'); ?>/contable/cliente/deudasvspagos/cortefecha/' + $('#fecha_desde').val().replaceAll('/', '-') + '/' + $('#fecha_hasta').val().replaceAll('/', '-'), '_self');
            }
        });
        $('#fact_contable_check').iCheck({
            checkboxClass: 'icheckbox_flat-blue',
            increaseArea: '20%' // optional
        });
        $('.select2_cuentas').select2({
            placeholder: "Escriba el nombre del cliente",
            allowClear: true,
            ajax: {
                url: '{{route("venta.clientesearch")}}',
                data: function(params) {
                    var query = {
                        search: params.term,
                        type: 'public'
                    }
                    return query;
                },
                processResults: function(data) {
                    // Transforms the top-level key of the response object from 'items' to 'results'

                    return {
                        results: data
                    };
                }
            }
        });

    });
    $(function() {
        $('.infinite-scroll').jscroll({
            autoTrigger: true,
            loadingHtml: '<img class="center-block" src="{{asset("/loading.gif")}}" width="50px" alt="Loading..." />',
            padding: 0,
            nextSelector: '.pagination li.active + li a',
            contentSelector: 'div.infinite-scroll',
            callback: function() {

                $('div.paginationLinks').remove();

            }
        });
    });
    $('#seguimiento').on('hidden.bs.modal', function() {
        $(this).removeData('bs.modal');
    });

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

    $("#btn_imprimir").click(function() {
        $("#print_reporte_master").submit();
        // document.getElementById("print_reporte_master").submit();
    });

    function excel() {
        $("#print_reporte_master").submit();
    }
    $("#nombre_proveedor").autocomplete({
        source: function(request, response) {
            $.ajax({
                url: "{{route('compra_buscar_nombreproveedor')}}",
                dataType: "json",
                data: {
                    term: request.term
                },
                success: function(data) {
                    response(data);
                }
            });
        },
        minLength: 2,
    });
    $('#fact_contable_check').on('ifChanged', function(event) {
        //aqui funciona si cambio el input time
        if ($(this).prop("checked")) {
            $("#esfac_contable").val(1);
            $("#es_fact_dos").val(1);
        } else {
            $("#esfac_contable").val(0);
        }

    });
    $('#example2').DataTable({
        'paging': false,
        dom: 'lBrtip',
        'lengthChange': false,
        'searching': true,
        "scrollX": true,
        "scrollY": 450,
        'ordering': false,
        responsive: true,
        'info': false,
        'autoWidth': true,
        buttons: [{
                extend: 'copyHtml5',
                footer: true
            },
            {
                extend: 'excelHtml5',
                footer: true,
                title: 'INFORME DEUDAS VS PAGOS'
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
                title: 'INFORME DEUDAS VS PAGOS',
                customize: function(doc) {
                    doc.pageMargins = [30, 30, 30, 30];
                    doc.styles.title = {
                        color: 'black',
                        fontSize: '16',
                        alignment: 'center'
                    }
                }
            }
        ],
    });

    function cambiar_nombre_proveedor() {
        $.ajax({
            type: 'post',
            url: "{{route('compra_buscar_proveedornombre')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'nombre': $("#nombre_proveedor").val()
            },
            success: function(data) {
                if (data.value != "no") {
                    $('#id_proveedor').val(data.value);
                    $('#id_proveedor2').val(data.value);
                    $('#direccion_id_proveedor').val(data.direccion);
                } else {
                    $('#id_proveedor').val("");
                    $('#id_proveedor2').val("");
                    $('#direccion_proveedor').val("");
                }

            },
            error: function(data) {

            }
        });
    }

    $(function() {
        $('#fecha_desde').datetimepicker({
            format: 'YYYY/MM/DD',
            defaultDate: '{{$fecha_desde}}',
        });
        $('#fecha_hasta').datetimepicker({
            format: 'YYYY/MM/DD',
            defaultDate: '{{$fecha_hasta}}',

        });
        $("#fecha_desde").on("dp.change", function(e) {
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

    function verifica_fechas() {
        if (Date.parse($("#fecha_desde").val()) > Date.parse($("#fecha_hasta").val())) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Verifique el rango de fechas y vuelva consultar'
            });
        }
    }
</script>
@endsection