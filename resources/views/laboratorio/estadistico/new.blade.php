@extends('laboratorio.estadistico.base')
@section('action-content')
<style>
    g shapering-rendering {
        display: none !important;
    }
</style>
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<section class="content">
    <div class="box">
        <div class="box-body">
            <form action="{{route('e.labs_estadisticos')}}" method="POST">
                {{ csrf_field() }}
                <div class="form-group">
                    <div class="col-md-1">
                        <label>Fecha Desde </label>

                    </div>
                    <div class="col-md-2">
                        <input class="form-control" type="text" name="fecha" id="fecha" @if(!is_null($fecha)) value="{{$fecha}}" @endif >
                    </div>
                    <div class="col-md-1">
                        <label>Fecha Hasta </label>
                    </div>
                    <div class="col-md-2">
                        <input class="form-control" type="text" name="fechafin" id="fechafin" @if(!is_null($fechafin)) value="{{$fechafin}}" @endif >
                    </div>
                    <div class="col-md-1">
                        <label>Examenes</label>
                    </div>
                    <div class="col-md-3">
                        <select class="form-control select2" name="exam[]" id="exam" multiple="multiple">
                            <option value="">Seleccione ...</option>
                            @foreach($listadoex as $list)
                                <option @if(count($id_exam)>0) @if(in_array($list->id, $id_exam)) selected @endif @endif value="{{$list->id}}"> {{$list->descripcion}}</option>
                            @endforeach
                                <option @if(count($id_exam)>0) @if(in_array('-10', $id_exam)) selected @endif @endif value="-10">TODOS</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        
                        <button class="btn btn-success btn-gray"> <i class="fa fa-search"></i> Buscar </button>
                        <button class="btn btn-primary btn-gray" onclick="printDiv()"> <i class="fa fa-print"></i> </button>

                    </div>
                </div>
            </form>

            <div class="col-md-12"> 
                &nbsp;  
            </div>
            <ul class="nav nav-tabs">
                <li class="active"><a data-toggle="tab" href="#home">Tabla</a></li>
                <li><a data-toggle="tab" href="#menu1">Barras</a></li>
                <li><a data-toggle="tab" href="#menu2">Pastel</a></li>
            </ul>
            <div class="tab-content">
                <div id="home" class="tab-pane fade in active">
                <div class="col-md-12" style="margin-top: 10px;">
                @php 
                    $pr= date('m',strtotime($fecha));
                    $mes="";
                    switch($pr){
                        case '01':
                          $mes="ENERO";
                          break;
                        case '02':
                          $mes="FEBRERO";
                          break;
                        case '03':
                          $mes="MARZO";
                          break;
                        case '04':
                          $mes="ABRIL";
                          break;
                        case '05':
                          $mes="MAYO";
                          break;
                        case '06':
                          $mes="JUNIO";
                          break;
                        case '07':
                          $mes="JULIO";
                          break;
                        case '08':
                          $mes="AGOSTO";
                          break;
                        case '09':
                          $mes="SEPTIEMBRE";
                          break;
                        case '10':
                          $mes="OCTUBRE";
                          break;
                        case '11':
                          $mes="NOVIEMBRE";
                          break;
                        case '12':
                          $mes="DICIEMBRE";
                          break;
                        
                    }
                @endphp
                <label>EXÁMENES DESDE {{date('Y/m/d',strtotime($fecha))}} HASTA {{date('Y/m/d',strtotime($fechafin))}} </label>
                </div>
                    <div class="table table-responsive">
                        <table id="table2" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Exámenes</th>
                                    <th>Cantidad</th>
                                    <th>Totales</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($examenes as $v)
                                <tr>
                                    <td>{{$v->descripcion}}</td>
                                    <td>{{$v->cantidad}}</td>
                                    <td>{{number_format($v->total,2)}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
<!--                     <div class="col-md-12">
                      <label>EXÁMENES AÑO {{date('Y',strtotime($fecha))}}</label>
                    </div>
                    <div class="table table-responsive">
                        <table id="table3" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Exámenes</th>
                                    <th>Cantidad</th>
                                    <th>Totales</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($examenesanio as $vs)
                                <tr>
                                    <td>{{$vs->descripcion}}</td>
                                    <td>{{$vs->cantidad}}</td>
                                    <td>{{number_format($vs->total,2)}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div> -->

                </div>
                <div id="menu1" class="tab-pane fade">
                      <div class="col-md-12 col-xs-12" id="chartdiv2" style="height: 2100px; width:100%; margin-top:25px;">
                        </div>
                </div>
                <div id="menu2" class="tab-pane fade">
                <div id="imprimir">
                       
                       <div class="col-md-12 col-xs-12" id="chartdiv" style="height: 2100px; width:100%; margin-top:25px;">
                       </div>
                   </div>
                </div>
            </div>



        </div>
    </div>
</section>
<script src="https://cdn.amcharts.com/lib/4/core.js"></script>
<script src="https://cdn.amcharts.com/lib/4/charts.js"></script>
<script src="https://cdn.amcharts.com/lib/4/themes/animated.js"></script>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#table2').DataTable({
            'paging': true,
            'lengthChange': true,
            'searching': true,
            'ordering': true,
            'info': false,
            'autoWidth': false,
            'sInfoEmpty': true,
            'sInfoFiltered': true,
            'order': [
                [1, "desc"]
            ],
            dom: 'lBrtip',
            buttons: [
                'copyHtml5',
                'excelHtml5',
                'csvHtml5',
                'pdfHtml5'
            ],
            'language': {
                "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            },
        });
        $('#table3').DataTable({
            'paging': true,
            'lengthChange': true,
            'searching': true,
            'ordering': true,
            'info': false,
            'autoWidth': false,
            'sInfoEmpty': true,
            'sInfoFiltered': true,
            'order': [
                [1, "desc"]
            ],
            dom: 'lBrtip',
            buttons: [
                'copyHtml5',
                'excelHtml5',
                'csvHtml5',
                'pdfHtml5'
            ],
            'language': {
                "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            },
        });
        $('#fecha').datetimepicker({
          format: 'YYYY-MM-DD',
        });
        $('#fechafin').datetimepicker({
          format: 'YYYY-MM-DD',
        });
       
    });

    $('.select2').select2({
        tags: false
    });

    function printDiv(nombreDiv) {
        Popup($('<div/>').append($("#imprimir").clone()).html());
        Popup($('<div/>').append($("#imprimir2").clone()).html());
    }

    // WORKS AT NOVEMBER 23 2020
    function Popup(data) {
        var mywindow = window.open('', 'my div', 'height=400,width=600');
        mywindow.document.write('<html><head><title>SISTEMA MÉDICO</title>');
        mywindow.document.write('<link rel="stylesheet" href="{{ asset("/bower_components/AdminLTE/dist/css/AdminLTE.min.css")}}" type="text/css" />');
        mywindow.document.write('<style>   .hidden-paginator {display: none;} </style>');
        mywindow.document.write('</head><body >');
        mywindow.document.write(data);
        mywindow.document.write('</body></html>');
        mywindow.print();
        //  mywindow.close();
        return true;
    }


    function Popup2(data) {
        var mywindow = window.open('', 'my div', 'height=400,width=600');
        mywindow.document.write('<html><head><title>SISTEMA MÉDICO</title>');
        mywindow.document.write('<link rel="stylesheet" href="{{ asset("/bower_components/AdminLTE/dist/css/AdminLTE.min.css")}}" type="text/css" />');
        mywindow.document.write('<style>   .hidden-paginator {display: none;} </style>');
        mywindow.document.write('</head><body >');
        mywindow.document.write(data);
        mywindow.document.write('</body></html>');
        mywindow.print();
        //  mywindow.close();
        return true;
    }


    am4core.ready(function() {

        // Themes begin
        am4core.useTheme(am4themes_animated);
        // Themes end

        // Create chart instance
        var chart = am4core.create("chartdiv", am4charts.PieChart);

        // Add data
        chart.data = [@foreach($examenes as $e) {
                @if($e->total>0)
                "descripcion": "{{$e->descripcion}}",
                "total": "{{round($e->total,2)}}"
                @endif
            },
            @endforeach
        ];

        // Add and configure Series
        var pieSeries = chart.series.push(new am4charts.PieSeries());
        pieSeries.dataFields.value = "total";
        pieSeries.dataFields.category = "descripcion";
        pieSeries.slices.template.stroke = am4core.color("#fff");
        pieSeries.slices.template.strokeWidth = 2;
        pieSeries.slices.template.strokeOpacity = 1;
        chart.legend = new am4charts.Legend();

        //#2
        // This creates initial animation
        var chart = am4core.create("chartdiv2", am4charts.XYChart);

        // Add data
        chart.data = [@foreach($examenes as $e) {
                @if($e->total>0)
                "descripcion": "{{$e->descripcion}}",
                "total": "{{round($e->total,2)}}"
                @endif
            },
            @endforeach
        ];
        var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
        categoryAxis.dataFields.category = "descripcion";
        categoryAxis.renderer.grid.template.location = 0;
        categoryAxis.renderer.minGridDistance = 50;

        categoryAxis.renderer.labels.template.adapter.add("dy", function(dy, target) {
        if (target.dataItem && target.dataItem.index & 2 == 2) {
            return dy + 25;
        }
        return dy;
        });

        var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());

        // Create series
        var series = chart.series.push(new am4charts.ColumnSeries());
        series.dataFields.valueY = "total";
        series.dataFields.categoryX = "descripcion";
        series.name = "Total";
        series.columns.template.tooltipText = "{categoryX}: [bold]{valueY}[/]";
        series.columns.template.fillOpacity = .8;

        var columnTemplate = series.columns.template;
        columnTemplate.strokeWidth = 2;
        columnTemplate.strokeOpacity = 1;
        

    });
</script>

@endsection