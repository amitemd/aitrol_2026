@extends('contable.balance_general.base')
@section('action-content')
<style>
    .hover :hover {
        color: green;
        cursor: pointer;
        font-weight: bold;
    }

    .redireccionar:hover {
        color: green;
        cursor: pointer;
        font-weight: bold;
    }

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
        margin-left: 60px;
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

    .table-condensed>thead>tr>th>td,
    .table-condensed>tbody>tr>th>td,
    .table-condensed>tfoot>tr>th>td,
    .table-condensed>thead>tr>td,
    .table-condensed>tbody>tr>td,
    .table-condensed>tfoot>tr>td {
        padding: 0.5px;
        line-height: 1;
    }
</style>

<!-- Ventana modal editar -->
<link rel="stylesheet" href="{{ asset('/css/bootstrap-datetimepicker.css')}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<!-- Main content -->
<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Contable</a></li>
            <li class="breadcrumb-item"><a href="#">Contabilidad</a></li>
            <li class="breadcrumb-item"><a href="../">Balance General</a></li>
        </ol>
    </nav>
    <div class="box">
        <!-- <div class="box-header with-border" style="color: black; font-family: 'Helvetica general3';">
            <div class="col-md-6">
              <h3 class="box-title">Criterios de búsqueda</h3>
            </div>
        </div> -->

        <div class="row head-title">
            <div class="col-md-12 cabecera">
                <label class="color_texto" for="title">BUSCADOR</label>
            </div>
        </div>

        <!-- /.box-header -->
        <form method="POST" action="{{ route('libro_mayor.index') }}" style="display:none" target="_blank">
            {{ csrf_field() }}
            <input type="text" name="fecha" id="fechan">
            <input type="text" name="fecha_hasta" id="fecha_hastan">
            <input type="text" name="cuenta" id="cuentan">
            <input type="text" id="cuenta_hasta" name="cuenta_hasta">
            <input type="submit" id="guardar">
        </form>
        <div class="box-body dobra">
            <form method="POST" id="reporte_master" action="{{ route('balance_general.show') }}">
                {{ csrf_field() }}

                <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
                    <label for="fecha" class="texto col-md-3 control-label">Fecha desde:</label>
                    <div class="col-md-9">
                        <div class="input-group date">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" class="form-control input-sm" name="fecha_desde" id="fecha_desde" value="<?= date('d/m/Y', strtotime($desde)); ?>" required autocomplete="off">
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
                            <input type="text" class="form-control input-sm" name="fecha_hasta" id="fecha_hasta" value="<?= date('d/m/Y', strtotime($hasta)); ?>" required autocomplete="off">
                            <div class="input-group-addon">
                                <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_hasta').value = ''; buscar();"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
                    <label for="lbl_cuentas_detalle" class="texto col-md-5 control-label">Mostrar cuentas de detalle</label>
                    <input type="checkbox" id="cuentas_detalle" class="flat-green" name="cuentas_detalle" value="1" @if(@$cuentas_detalle=="1" ) checked @endif>
                </div>
                <div class="form-group col-md-6 col-xs-9 pull-right" style="text-align: right;">
                    <button type="submit" class="btn btn-primary" id="boton_buscar">
                        <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar
                    </button>
                    <button type="button" class="btn btn-primary" id="btn_imprimir">
                        <span class="glyphicon glyphicon-print" aria-hidden="true"></span> Imprimir
                    </button>
                    <button type="button" class="btn btn-primary" id="btn_exportar">
                        <span class="glyphicon glyphicon-save-file" aria-hidden="true"></span> Exportar
                    </button>
                    <button type="button" class="btn btn-primary" id="btn_exportarxml">
                        <span class="glyphicon glyphicon-save-file" aria-hidden="true"></span> Exportar XLS
                    </button>
                </div>
            </form>
        </div>
        <input type="hidden" id="id_empresa" value="{{$empresa->id}}">
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-solid">
                        <div class="box-header with-border">
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <div class="izq col-md-1 clearfix">
                                <dl class="izquierda">
                                    @if(!is_null($empresa->logo))
                                    <dd><img src="{{asset('/logo').'/'.$empresa->logo}}" alt="Logo Image" style="width:80px;height:80px;" id="logo_empresa"></dd>
                                    @endif
                                </dl>
                            </div>
                            <div class="col-md-3">
                                <dl>
                                    <dd><strong>{{$empresa->nombrecomercial}}</strong></dd>
                                    <dd>&nbsp; {{$empresa->id}}</dd>
                                </dl>
                            </div>
                            <div class="col-md-4">
                                <h4 style="text-align: center;">Estado de situaci&oacute;n financiera</h4>
                                <h5 style="text-align: center;">Del desde <?= $desde; ?> <br> al hasta <?= $hasta; ?></h5>
                            </div>
                            <div class="col-md-4">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="table-responsive col-md-12">
                    <div class="content">
                        <div style="padding: 0px 0px 0px 0px;font-size: 9px;">
                            <?php
                            echo $html;
                            ?>
                        </div>
                        <div class="col-md-12" style="margin-top:10%">
                            &nbsp;
                        </div>
                        <div class="col-md-12">
                            <div class="row">
                                <div class=" col-md-6">
                                    @if(!is_null($empresa->id_representante))
                                    @if($empresa->persona_nat_jur == '2')
                                    <div class="row">
                                        <label class="col-md-12">{{trans('contableM.RepresentanteLegal')}}</label>
                                        @if(is_null($empresa->tipo_representante))
                                        <label class="col-md-12">{{$empresa->pref_repre->titulo_prefijo}} {{$empresa->usuario_representante->nombre1}} {{$empresa->usuario_representante->nombre2}} {{$empresa->usuario_representante->apellido1}} {{$empresa->usuario_representante->apellido2}}
                                        </label>
                                        <label class="col-md-12">C.I. {{$empresa->id_representante}}</label>
                                        @else
                                        <label class="col-md-12">{{$empresa->pref_repre->titulo_prefijo}} {{$empresa->usuario_representante->nombre1}} {{$empresa->usuario_representante->nombre2}} {{$empresa->usuario_representante->apellido1}} {{$empresa->usuario_representante->apellido2}} EN REPRESENTACION DE {{$empresa->empresa_representante}}
                                        </label>
                                        @endif
                                    </div>
                                    @endif
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <div class="row" style="float:right">
                                        @if(!is_null($empresa->id_contador))
                                        <label class="col-md-12">{{trans('contableM.ContadoraGeneral')}}</label> <br>
                                        <label class="col-md-12">{{$empresa->pref_cont->titulo_prefijo}} {{$empresa->usuario_contador->nombre1}} {{$empresa->usuario_contador->nombre2}} {{$empresa->usuario_contador->apellido1}} {{$empresa->usuario_contador->apellido2}}</label> <br>
                                        <label class="col-md-12">C.I. {{$empresa->id_contador}}</label> <br>
                                        <label class="col-md-12">Registro # {{$empresa->num_registro_contador}}</label> <br>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<form method="POST" id="print_reporte_master" action="{{ route('balance_general.show') }}" target="_blank">
    {{ csrf_field() }}
    <input type="hidden" name="filfecha_desde" id="filfecha_desde" value="">
    <input type="hidden" name="filfecha_hasta" id="filfecha_hasta" value="">
    <input type="hidden" name="filcuentas_detalle" id="filcuentas_detalle" value="">
    <input type="hidden" name="filmostrar_detalles" id="filmostrar_detalles" value="">
    <input type="hidden" name="exportar" id="exportar" value="0">
    <input type="hidden" name="imprimir" id="imprimir" value="">
</form>
<!-- /.content -->
<script src="https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<script src="{{ asset ('/js/jquery.validate.js') }}"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script src="{{ asset ('/plugins/datetimepicker/bootstrap-material-datetimepicker.js') }}"></script>
<script src="{{ asset ('/js/bootstrap-datetimepicker.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('.select2_cuentas').select2({
            tags: false
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
        //console.log("si es valida")
        let idEmpresa = document.getElementById('id_empresa').value;
        if (idEmpresa != '0992704152001') {
            console.log("entra al if")
            imprimirElemento();

        } else {
            console.log("No entra al if")
            $("#filfecha_desde").val($("#fecha_desde").val());
            $("#filfecha_hasta").val($("#fecha_hasta").val());
            $("#filcuentas_detalle").val($("#cuentas_detalle").val());
            $("#filmostrar_detalles").val($("#mostrar_detalles").val());
            $("#print_reporte_master").submit();
        }
    });

    $("#btn_exportar").click(function() {
        $("#filfecha_desde").val($("#fecha_desde").val());
        $("#filfecha_hasta").val($("#fecha_hasta").val());
        if ($("#cuentas_detalle").prop("checked")) {
            $("#filcuentas_detalle").val(1);
        } else {
            $("#filcuentas_detalle").val("");
        }
        $("#filmostrar_detalles").val($("#mostrar_detalles").val());
        $("#exportar").val(1);
        $("#print_reporte_master").submit();
    });

    $(document).ready(function() {

        tinymce.init({
            selector: '#hc'
        });

        $('input[type="checkbox"].flat-green').iCheck({
            checkboxClass: 'icheckbox_flat-green',
            radioClass: 'iradio_flat-green'
        });

        $('input[type="checkbox"].flat-red').iCheck({
            checkboxClass: 'icheckbox_flat-red',
            radioClass: 'iradio_flat-red'
        });
        $('#btn_exportarxml').click(function() {
            exportTableToExcel('example2_', 'Balance de Resultado');
        });

    });

    $(function() {
        $('#fecha_desde').datetimepicker({
            // format: 'YYYY/MM/DD',
            format: 'DD/MM/YYYY',
        });
        $('#fecha_hasta').datetimepicker({
            // format: 'YYYY/MM/DD',
            format: 'DD/MM/YYYY',
        });
        $("#fecha_desde").on("dp.change", function(e) {
            verifica_fechas();
        });

        $("#fecha_hasta").on("dp.change", function(e) {
            verifica_fechas();
        });
    });

    function exportTableToExcel(tableID, filename = '') {
        var downloadLink;
        var dataType = 'application/vnd.ms-excel';
        var tableSelect = document.getElementById(tableID);
        var tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');

        // Specify file name
        filename = filename ? filename + '.xls' : 'excel_data.xls';

        // Create download link element
        downloadLink = document.createElement("a");

        document.body.appendChild(downloadLink);

        if (navigator.msSaveOrOpenBlob) {
            var blob = new Blob(['ufeff', tableHTML], {
                type: dataType
            });
            navigator.msSaveOrOpenBlob(blob, filename);
        } else {
            // Create a link to the file
            downloadLink.href = 'data:' + dataType + ', ' + tableHTML;

            // Setting the file name
            downloadLink.download = filename;

            //triggering the function
            downloadLink.click();
        }
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

    function buscar() {
        var obj = document.getElementById("boton_buscar");
        obj.click();
    }
    //imprimir
    function imprimirElemento() {
        var ventana = window.open('', 'PRINT');
        var elemento = document.getElementById('example2_wrapper').innerHTML;
        //http://www.ieced.siaam.ec/sis_medico/public/bower_components/AdminLTE/bootstrap/css/bootstrap.min.css

        ventana.document.write('<html><head><title>' + document.title + '</title>');
        ventana.document.write('<link rel="stylesheet" href="http://www.ieced.siaam.ec/sis_medico/public/bower_components/AdminLTE/bootstrap/css/bootstrap.min.css">'); //Aquí agregué la hoja de estilos
        ventana.document.write('<link href="http://192.168.75.51/sis_medico/public/css/app-template.css" rel="stylesheet">');
        ventana.document.write('</head><body >');
        ventana.document.write(estilos()); //Aquí agregué la hoja de estilos
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

    function estilos() {
        let estilos_tablas = `
      <style>

      .row{
        width:80%;
        float:left;
      }
      .col-md-8 {
        width:66.66666667%,
        float: left;

      }

      .col-md-5 {
        width:41.66666667%,
        float: left;
      }

      .col-md-6 {
        width: 50%;
        float: left;
      }
      .col-md-12 {
        width: 100%;
        position: relative;
      }
      .well-dark {
          background-color: #aaa;
          width: 100%;
          color: #000000;
      }
      .table {
          width: 100%;
      }
      table.dataTable {
          clear: both;
          margin-top: 6px !important;
          margin-bottom: 6px !important;
          max-width: none !important;
      }
    table {
        border-spacing: 0;
        border-collapse: collapse;
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
    p.t3 {
        font-size: 10px;
    }
    p.s4 {
      margin-left: 60px;
      font-size: 10px;
    }
    .derecha{
      float: right;
    }
    .izquierda{
      padding-right:10px;
      float: left;

    }
    .izq{
      width:45%;
    }
    th{
      color: #010101;
    }
    .titulos{
      margin-top:50px;
    }

      </style>`;
        return estilos_tablas;
    }

    function redireccionar(campo) {
        //console.log(campo);
        var fechaDesde = document.querySelector("#fecha_desde").value;
        var fechaHasta = document.querySelector('#fecha_hasta').value;
        document.getElementById('fechan').value = fechaDesde;
        document.getElementById('fecha_hastan').value = fechaHasta;
        document.getElementById('cuentan').value = campo;
        document.getElementById('cuenta_hasta').value = campo;
        $("#guardar").click();
    }
</script>
@endsection