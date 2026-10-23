@extends('contable.balance_comprobacion.base')
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

    .table-striped>thead>tr>th>td,
    .table-striped>tbody>tr>th>td,
    .table-striped>tfoot>tr>th>td,
    .table-striped>thead>tr>td,
    .table-striped>tbody>tr>td,
    .table-striped>tfoot>tr>td {
        padding: 0.5px;
        line-height: 1;
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

    .text-left {
        text-align: left;
    }
</style>
<!-- Ventana modal editar -->
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.2/css/jquery.dataTables.min.css">
<!-- Main content -->
<section class="content">

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Contable</a></li>
            <li class="breadcrumb-item"><a href="#">Contabilidad</a></li>
            <li class="breadcrumb-item"><a href="#">Acreedores</a></li>
            <li class="breadcrumb-item"><a href="#">Edad de cartera</a></li>

        </ol>
    </nav>
    <div class="box" style=" background-color: white;">
        <div class="row head-title">
            <div class="col-md-12 cabecera">
                <label class="color_texto" for="title">BUSCADOR</label>
            </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body dobra">
            <form method="POST" id="reporte_master" action="{{ route('contable.reporte_edad_cartera') }}">
                {{ csrf_field() }}
                <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
                    <label for="fecha" class="texto col-md-3 control-label">Fecha desde:</label>
                    <div class="col-md-9">
                        <div class="input-group date">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="date" class="form-control input-sm" name="d" id="d" value="{{$fecha_desde}}">
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
                            <input type="date" class="form-control input-sm" name="h" id="h" autocomplete="off" value="{{$fecha_hasta}}">
                        </div>
                    </div>
                </div>
                <div class="form-group col-md-6 col-xs-9 pull-right" style="text-align: right;">
                    <button type="submit" class="btn btn-primary" id="boton_buscar">
                        <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar
                    </button>
                </div>
            </form>
        </div>
        <!-- /.box-body -->
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table id="example" class="table table-striped table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th style="background-color: #695cf4ff;" colspan="2">MAS RECIENTE</th>
                                    <th style="background-color: #695cf4ff;" colspan="2">MAS ANTIGUO</th>
                                    <th></th>
                                </tr>
                                <tr style="background-color: #a9a7c5ff;">
                                    <th>Proveedores</th>
                                    <th style="text-align: center;">1. Corriente / 30 Días</th>
                                    <th style="text-align: center;">2. 31/60 Días</th>
                                    <th style="text-align: center;">3. 61/90 Días</th>
                                    <th style="text-align: center;">4. Más de 90 Días</th>
                                    <th style="text-align: center;">Total general</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($informe['detalle'] as $edad)
                                <tr style="background-color: #8ebff0ff;">
                                    <td style="font-weight: bold; text-align: center; ">{{ $edad['nombre_tipo'] }}</td>
                                    <td style="text-align: right;">{{ $edad['tipo1'] }}</td>
                                    <td style="text-align: right;">{{ $edad['tipo2'] }}</td>
                                    <td style="text-align: right;">{{ $edad['tipo3'] }}</td>
                                    <td style="text-align: right;">{{ $edad['tipo4'] }}</td>
                                    <td style="text-align: right;">{{ $edad['saldo'] }}</td>
                                </tr>
                                @php
                                usort($edad['detalle'], function($a, $b) {
                                return $b['total_saldo'] <=> $a['total_saldo'];
                                    });
                                    @endphp
                                    @foreach($edad['detalle'] as $detalle)
                                    <tr>
                                        <td style="font-weight: 500">{{ $detalle['nombre_proveedor'] }}</td>
                                        <td style="text-align: right;">{{ $detalle['tipo_caducidad1'] }}</td>
                                        <td style="text-align: right;">{{ $detalle['tipo_caducidad2'] }}</td>
                                        <td style="text-align: right;">{{ $detalle['tipo_caducidad3'] }}</td>
                                        <td style="text-align: right;">{{ $detalle['tipo_caducidad4'] }}</td>
                                        <td style="text-align: right;">{{ $detalle['total_saldo'] }}</td>
                                    </tr>
                                    @endforeach
                                    @endforeach
                            </tbody>
                            <tfoot>
                                <tr style="background-color: #a9a7c5ff;">
                                    <th>TOTAL GENERAL</th>
                                    <th style="text-align: right;">{{ $informe['totales']['total_caducidad1'] }}</th>
                                    <th style="text-align: right;">{{ $informe['totales']['total_caducidad2'] }}</th>
                                    <th style="text-align: right;">{{ $informe['totales']['total_caducidad3'] }}</th>
                                    <th style="text-align: right;">{{ $informe['totales']['total_caducidad4'] }}</th>
                                    <th style="text-align: right;">{{ $informe['totales']['total_saldo_global'] }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>

<script type="text/javascript">
    $('#example').DataTable({
        'paging': false,
        "scrollX": true,
        "scrollY": 550,
        dom: 'lBrtip',
        'lengthChange': false,
        'searching': true,
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
                title: 'EDAD DE CARTERA {{$empresa->nombrecomercial}} @if($fecha_desde!=null) {{date("d/m/Y",strtotime($fecha_desde))}} @endif -  {{date("d/m/Y",strtotime($fecha_hasta))}}',
            },
            {
                extend: 'csvHtml5',
                footer: true
            },
            {
                extend: 'pdfHtml5',
                orientation: 'landscape',
                title: function() {
                    return "ABCDE List";
                },
                pageSize: 'A3',
                footer: true,
                title: 'CUENTAS POR PAGAR {{$empresa->nombrecomercial}}',
                customize: function(doc) {
                    doc.styles.title = {
                        color: 'black',
                        fontSize: '17',
                        alignment: 'center'
                    }
                }
            }
        ],
    })
</script>

@endsection