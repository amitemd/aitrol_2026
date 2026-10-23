@extends('archivo_plano.archivo.base')
@section('action-content')
<style>
    g shapering-rendering {
        display: none !important;
    }
</style>

<section class="content">
    <div class="box">
        <div class="box header">
            <label> Estadisticos Convenios Publicos por Procedimientos</label>
        </div>
        <div class="box-body">
            <form action="{{route('estaditicos_plano.index')}}" method="POST">
                {{ csrf_field() }}
                <div class="form-group">
                    <div class="col-md-2">
                        <label>Fecha desde </label>

                    </div>
                    <div class="col-md-2">
                        <input class="form-control" type="date" name="fechaini" id="fechaini" value="{{$fechaini}}">
                    </div>
                    <div class="col-md-2">
                        <label> Fecha hasta</label>

                    </div>
                    <div class="col-md-2">
                        <input class="form-control" type="date" name="fechafin" id="fechafin" value="{{$fechafin}}">
                    </div>
                    <!--<div class="col-md-2">
                        <select class="form-control select2" name="id_procedimiento" id="id_procedimiento">
                            <option value="">Seleccione...</option>
                            @foreach($procedimiento as $x)
                            <option @if($id_procedimiento==$x->id) selected="selected" @endif value="{{$x->id}}">{{$x->nombre}}</option>
                            @endforeach
                        </select>
                    </div>-->


                    <div class="col-md-2">
                        <select class="form-control select2" name="id_procedimiento" id="id_procedimiento">
                            <option @if($id_procedimiento=='ENDOSCOPIA DIGESTIVA ALTA') selected @endif value="ENDOSCOPIA DIGESTIVA ALTA">ENDOSCOPIA DIGESTIVA ALTA</option>
                            <option @if($id_procedimiento=='BRONCOSCOPIA') selected @endif value="BRONCOSCOPIA" >BRONCOSCOPIA</option>         
                            <option @if($id_procedimiento=='COLONOSCOPIA') selected @endif value="COLONOSCOPIA" >COLONOSCOPIA</option>
                            <option @if($id_procedimiento=='CPRE') selected @endif value="CPRE" >CPRE</option>
                            <option @if($id_procedimiento=='ECOENDOSCOPIA') selected @endif value="ECOENDOSCOPIA" >ECOENDOSCOPIA</option>
                            <option @if($id_procedimiento=='ENTEROSCOPIA') selected @endif value="ENTEROSCOPIA" >ENTEROSCOPIA</option>
                            <option @if($id_procedimiento=='FUNCIONAL') selected @endif value="FUNCIONAL" >FUNCIONAL</option>
                        </select>
                    </div>


                    <div class="col-md-2">
                        <button class="btn btn-success btn-gray"> <i class="fa fa-search"></i> Buscar </button>
                        <button class="btn btn-primary btn-gray" onclick="printDiv()"> <i class="fa fa-print"></i> </button>

                    </div>
                </div>
            </form>
         
            <div class="table table-responsive col-md-12" style="margin-top: 10px;">
                <table id="example2" class="table table-hover dataTable" role="grid" aria-describedby="example2_info">
                    <thead>
                        <tr>
                            <th>Procedimiento</th>
                            <th>Valor</th>
                        </tr>

                    </thead>
                    <tbody>
                        @foreach($archivo_plano as $s)
                        <tr>
                            <td>{{$s->nombre}}</td>
                            <td style="text-align: right;">$ {{number_format(round($s->total,2),2,'.',',')}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
            <div id="imprimir">
                <div style="height: 200px;"> </div>
                <div class="col-md-12 col-xs-12" id="chartdiv" style="height: 900px; margin-top:25px;">
                </div>
                <div class="col-md-12 col-xs-12" id="chartdiv2" style="height: 500px; margin-top: 10px;">
                </div>
                <div class="col-md-12 col-xs-12" id="chartdiv3" style="height: 500px; margin-top: 10px;">
                </div>
            </div>

            <center><h4>Procedimiento: {{$id_procedimiento}}</h4></center>

             <div id="imprimir2">
                <div style="height: 200px;"> </div>
                <div class="col-md-12 col-xs-12" id="chartdiv_2" style="height: 900px; margin-top:25px;">
                </div>
                <div class="col-md-12 col-xs-12" id="chartdiv2_2" style="height: 500px; margin-top: 10px;">
                </div>
                <div class="col-md-12 col-xs-12" id="chartdiv3_2" style="height: 500px; margin-top: 10px;">
                </div>
            </div>


        </div>
    </div>
</section>
<script src="https://cdn.amcharts.com/lib/4/core.js"></script>
<script src="https://cdn.amcharts.com/lib/4/charts.js"></script>
<script src="https://cdn.amcharts.com/lib/4/themes/animated.js"></script>
<script>
    $(document).ready(function() {
        $('#example2').DataTable({
            'paging': true,
            'lengthChange': true,
            'searching': true,
            'ordering': true,
            'info': false,
            'autoWidth': false,
            'sInfoEmpty': true,
            'sInfoFiltered': true,
            'order'       : [[ 1, "desc" ]],
            'language': {
                "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            },
        });
        cargar_estad();

    });

    $('.select2').select2({
        tags: false
    });

    function cargar_estad(){
        $.ajax({
            type: 'get',
            url:"{{ route('masivo.masivo_carga_archivo_plano')}}",
            datatype: 'json',
            
            success: function(data){
                    
            },
            error: function(data){
                console.log(data);
            }
        });
    }

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
        chart.data = [@foreach($archivo_plano as $c) {
                "descripcion": "{{$c->nombre}}",
                "total": "{{round($c->total,2)}}"
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
        pieSeries.hiddenState.properties.opacity = 1;
        pieSeries.hiddenState.properties.endAngle = -90;
        pieSeries.hiddenState.properties.startAngle = -90;
        var chart = am4core.create("chartdiv2", am4charts.XYChart);
        chart.padding(40, 40, 40, 40);

        var categoryAxis = chart.yAxes.push(new am4charts.CategoryAxis());
        categoryAxis.renderer.grid.template.location = 0;
        categoryAxis.dataFields.category = "descripcion";
        categoryAxis.renderer.minGridDistance = 1;
        categoryAxis.renderer.inversed = true;
        categoryAxis.renderer.grid.template.disabled = true;

        var valueAxis = chart.xAxes.push(new am4charts.ValueAxis());
        valueAxis.min = 0;

        var series = chart.series.push(new am4charts.ColumnSeries());
        series.dataFields.categoryY = "descripcion";
        series.dataFields.valueX = "total";
        series.tooltipText = "{valueX.value}"
        series.columns.template.strokeOpacity = 0;
        series.columns.template.column.cornerRadiusBottomRight = 5;
        series.columns.template.column.cornerRadiusTopRight = 5;

        var labelBullet = series.bullets.push(new am4charts.LabelBullet())
        labelBullet.label.horizontalCenter = "left";
        labelBullet.label.dx = 10;
        labelBullet.label.text = "{values.valueX.workingValue.formatNumber('#.0as')}";
        labelBullet.locationX = 1;

        // as by default columns of the same series are of the same color, we add adapter which takes colors from chart.colors color set
        series.columns.template.adapter.add("fill", function(fill, target) {
            return chart.colors.getIndex(target.dataItem.index);
        });

        categoryAxis.sortBySeries = series;
        chart.data = [@foreach($archivo_plano as $c) {
                "descripcion": "{{$c->nombre}}",
                "total": "{{round($c->total,2)}}"
            },
            @endforeach
        ];

        var chart = am4core.create("chartdiv3", am4charts.PieChart);
        chart.hiddenState.properties.opacity = 0; // this creates initial fade-in
        chart.data = [@foreach($archivo_plano as $c) {
                "descripcion": "{{$c->nombre}}",
                "total": "{{round($c->total,2)}}"
            },
            @endforeach
        ];
        chart.radius = am4core.percent(70);
        chart.innerRadius = am4core.percent(40);
        chart.startAngle = 180;
        chart.endAngle = 360;

        var series = chart.series.push(new am4charts.PieSeries());
        series.dataFields.value = "total";
        series.dataFields.category = "descripcion";

        series.slices.template.cornerRadius = 10;
        series.slices.template.innerCornerRadius = 7;
        series.slices.template.draggable = true;
        series.slices.template.inert = true;
        series.alignLabels = false;

        series.hiddenState.properties.startAngle = 90;
        series.hiddenState.properties.endAngle = 90;

        chart.legend = new am4charts.Legend();



        //3d 
        var chart = am4core.create("chartdiv3", am4charts.XYChart3D);

        // Add data

        chart.data = [@foreach($archivo_plano as $c) {
                "descripcion": "{{$c->nombre}}",
                "total": "{{round($c->total,2)}}"
            },
            @endforeach
        ];
        // Create axes
        let categoryAxis2 = chart.xAxes.push(new am4charts.CategoryAxis());
        categoryAxis2.dataFields.category = "descripcion";
        categoryAxis2.renderer.labels.template.rotation = 270;
        categoryAxis2.renderer.labels.template.hideOversized = false;
        categoryAxis2.renderer.minGridDistance = 20;
        categoryAxis2.renderer.labels.template.horizontalCenter = "right";
        categoryAxis2.renderer.labels.template.verticalCenter = "middle";
        categoryAxis2.tooltip.label.rotation = 270;
        categoryAxis2.tooltip.label.horizontalCenter = "right";
        categoryAxis2.tooltip.label.verticalCenter = "middle";

        let valueAxis2 = chart.yAxes.push(new am4charts.ValueAxis());
        valueAxis2.title.text = "Totales";
        valueAxis2.title.fontWeight = "bold";

        // Create series
        var series2 = chart.series.push(new am4charts.ColumnSeries3D());
        series2.dataFields.valueY = "total";
        series2.dataFields.categoryX = "descripcion";
        series2.name = "Totales";
        series2.tooltipText = "{categoryX}: [bold]{valueY}[/]";
        series2.columns.template.fillOpacity = .8;

        var columnTemplate = series2.columns.template;
        columnTemplate.strokeWidth = 2;
        columnTemplate.strokeOpacity = 1;
        columnTemplate.stroke = am4core.color("#FFFFFF");

        columnTemplate.adapter.add("fill", function(fill, target) {
            return chart.colors.getIndex(target.dataItem.index);
        })

        columnTemplate.adapter.add("stroke", function(stroke, target) {
            return chart.colors.getIndex(target.dataItem.index);
        })

        chart.cursor = new am4charts.XYCursor();
        chart.cursor.lineX.strokeOpacity = 0;
        chart.cursor.lineY.strokeOpacity = 0;

    }); // end am4core.ready()


/*****************************************/
am4core.ready(function() {

        // Themes begin
        am4core.useTheme(am4themes_animated);
        // Themes end

        // Create chart instance
        var chart = am4core.create("chartdiv_2", am4charts.PieChart);

        // Add data
        chart.data = [@foreach($archivo_plano2 as $c) {
                "descripcion": "{{$c->nombre}}",
                "total": "{{round($c->total,2)}}"
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
        pieSeries.hiddenState.properties.opacity = 1;
        pieSeries.hiddenState.properties.endAngle = -90;
        pieSeries.hiddenState.properties.startAngle = -90;
        var chart = am4core.create("chartdiv2_2", am4charts.XYChart);
        chart.padding(40, 40, 40, 40);

        var categoryAxis = chart.yAxes.push(new am4charts.CategoryAxis());
        categoryAxis.renderer.grid.template.location = 0;
        categoryAxis.dataFields.category = "descripcion";
        categoryAxis.renderer.minGridDistance = 1;
        categoryAxis.renderer.inversed = true;
        categoryAxis.renderer.grid.template.disabled = true;

        var valueAxis = chart.xAxes.push(new am4charts.ValueAxis());
        valueAxis.min = 0;

        var series = chart.series.push(new am4charts.ColumnSeries());
        series.dataFields.categoryY = "descripcion";
        series.dataFields.valueX = "total";
        series.tooltipText = "{valueX.value}"
        series.columns.template.strokeOpacity = 0;
        series.columns.template.column.cornerRadiusBottomRight = 5;
        series.columns.template.column.cornerRadiusTopRight = 5;

        var labelBullet = series.bullets.push(new am4charts.LabelBullet())
        labelBullet.label.horizontalCenter = "left";
        labelBullet.label.dx = 10;
        labelBullet.label.text = "{values.valueX.workingValue.formatNumber('#.0as')}";
        labelBullet.locationX = 1;

        // as by default columns of the same series are of the same color, we add adapter which takes colors from chart.colors color set
        series.columns.template.adapter.add("fill", function(fill, target) {
            return chart.colors.getIndex(target.dataItem.index);
        });

        categoryAxis.sortBySeries = series;
        chart.data = [@foreach($archivo_plano2 as $c) {
                "descripcion": "{{$c->nombre}}",
                "total": "{{round($c->total,2)}}"
            },
            @endforeach
        ];

        var chart = am4core.create("chartdiv3_2", am4charts.PieChart);
        chart.hiddenState.properties.opacity = 0; // this creates initial fade-in
        chart.data = [@foreach($archivo_plano as $c) {
                "descripcion": "{{$c->nombre}}",
                "total": "{{round($c->total,2)}}"
            },
            @endforeach
        ];
        chart.radius = am4core.percent(70);
        chart.innerRadius = am4core.percent(40);
        chart.startAngle = 180;
        chart.endAngle = 360;

        var series = chart.series.push(new am4charts.PieSeries());
        series.dataFields.value = "total";
        series.dataFields.category = "descripcion";

        series.slices.template.cornerRadius = 10;
        series.slices.template.innerCornerRadius = 7;
        series.slices.template.draggable = true;
        series.slices.template.inert = true;
        series.alignLabels = false;

        series.hiddenState.properties.startAngle = 90;
        series.hiddenState.properties.endAngle = 90;

        chart.legend = new am4charts.Legend();



        //3d 
        var chart = am4core.create("chartdiv3_2", am4charts.XYChart3D);

        // Add data

        chart.data = [@foreach($archivo_plano2 as $c) {
                "descripcion": "{{$c->nombre}}",
                "total": "{{round($c->total,2)}}"
            },
            @endforeach
        ];
        // Create axes
        let categoryAxis2 = chart.xAxes.push(new am4charts.CategoryAxis());
        categoryAxis2.dataFields.category = "descripcion";
        categoryAxis2.renderer.labels.template.rotation = 270;
        categoryAxis2.renderer.labels.template.hideOversized = false;
        categoryAxis2.renderer.minGridDistance = 20;
        categoryAxis2.renderer.labels.template.horizontalCenter = "right";
        categoryAxis2.renderer.labels.template.verticalCenter = "middle";
        categoryAxis2.tooltip.label.rotation = 270;
        categoryAxis2.tooltip.label.horizontalCenter = "right";
        categoryAxis2.tooltip.label.verticalCenter = "middle";

        let valueAxis2 = chart.yAxes.push(new am4charts.ValueAxis());
        valueAxis2.title.text = "Totales";
        valueAxis2.title.fontWeight = "bold";

        // Create series
        var series2 = chart.series.push(new am4charts.ColumnSeries3D());
        series2.dataFields.valueY = "total";
        series2.dataFields.categoryX = "descripcion";
        series2.name = "Totales";
        series2.tooltipText = "{categoryX}: [bold]{valueY}[/]";
        series2.columns.template.fillOpacity = .8;

        var columnTemplate = series2.columns.template;
        columnTemplate.strokeWidth = 2;
        columnTemplate.strokeOpacity = 1;
        columnTemplate.stroke = am4core.color("#FFFFFF");

        columnTemplate.adapter.add("fill", function(fill, target) {
            return chart.colors.getIndex(target.dataItem.index);
        })

        columnTemplate.adapter.add("stroke", function(stroke, target) {
            return chart.colors.getIndex(target.dataItem.index);
        })

        chart.cursor = new am4charts.XYCursor();
        chart.cursor.lineX.strokeOpacity = 0;
        chart.cursor.lineY.strokeOpacity = 0;

    }); // end am4core.ready()
</script>

@endsection