<div id="reload">
   
        <div class="box header">
            <label> Estadisticos</label>
        </div>
        <div class="box-body">
            <form id="form" method="POST">
                {{ csrf_field() }}
              
                <div class="row">

                    <div class="col-md-12" style="text-align: center; margin-bottom: 10px;">
                        @if($validate==0)

                        <input type="hidden" name="anio_validate" id="anio_validate" value="0">
                        <button class="btn btn-success btn-gray anx" type="button">Ver por año</button>

                        @else
                        <input type="hidden" name="anio_validate" id="anio_validate" value="1">
                        <button class="btn btn-success btn-gray anv" type="button">Ver por rango de fecha</button>
                        @endif
                    </div>
                    @if($validate==0)
                    <div class="col-md-2" id="fechadesde">
                        <label>Fecha desde </label>

                    </div>
                    <div class="col-md-2" id="fechahasta">
                        <input class="form-control" type="date" name="fechaini" id="fechaini" value="{{$fechaini}}">
                    </div>
                    <div class="col-md-2">
                        <label> Fecha hasta</label>

                    </div>
                    <div class="col-md-2">
                        <input class="form-control" type="date" name="fechafin" id="fechafin" value="{{$fechafin}}">
                    </div>
                    @else
                    <div class="col-md-2" style="text-align: center;">
                        <label> Seleccione año </label>
                    </div>
                    <div class="col-md-4">
                        <select class="form-control" name="anio" id="anio">
                            <option value="">Seleccione ...</option>
                            <option @if($anio=="2019" ) selected @endif value="2019">2019</option>
                            <option @if($anio=="2020" ) selected @endif value="2020">2020</option>
                            <option @if($anio=="2021" ) selected @endif value="2021">2021</option>
                        </select>
                    </div>
                    @endif
                    @php
                    $ms=0;
                    $ms2=0;
                    @endphp
                    <div class="col-md-2">

                        <select class="form-control select2" name="seguro[]" id="seguro" multiple="multiple">
                            <option value="">Seleccione...</option>
                            @foreach($seguros as $x)
                            @if(is_array($id_seguro))
                            <option @if(in_array($x->id, $id_seguro)) selected @endif value="{{$x->id}}">{{$x->nombre}}</option>
                            @else
                            <option value="{{$x->id}}">{{$x->nombre}}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-success btn-gray sx" type="button" id="butt"> <i class="fa fa-search"></i> Buscar </button>
                        <button class="btn btn-primary btn-gray" onclick="printDiv()"> <i class="fa fa-print"></i> </button>

                    </div>
                </div>
            </form>

            <div id="imprimir">

                <div class="col-md-12 col-xs-12" id="chartdiv" style="height: 500px; margin-top:25px;">
                </div>
                @if($validate==0)
                <div class="col-md-12" style="text-align: center;">
                    <label> FORMAS DE PAGO</label>
                </div>
                <div class="col-md-12 col-xs-12" id="chartdiv2" style="height: 500px; margin-top: 10px;">
                </div>
                <div class="col-md-12 col-xs-12" id="chartdiv3" style="height: 500px; margin-top: 10px;">
                </div>
                @endif
            </div>


        </div>
    
</div>

<script>
    $(document).ready(function() {


    });
    $('body').on('click', '.anx', function() {
        console.log("entra");
        $("#anio_validate").val(1);
        estadisticos_reales()
    });
    $('body').on('click', '.anv', function() {
        console.log("entra");
        $("#anio_validate").val(0);
        estadisticos_reales()
    });
    $('body').on('click', '.sx', function() {
        console.log("entra");
      
        estadisticos_reales()
    });
    $('.select2').select2({
        tags: false
    });

    function printDiv(nombreDiv) {
        Popup($('<div/>').append($("#imprimir").clone()).html());
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
    @if($validate == 0)
        am4core.ready(function() {

            // Themes begin
            am4core.useTheme(am4themes_animated);
            // Themes end

            // Create chart instance
            var chart = am4core.create("chartdiv", am4charts.PieChart);

            // Add data
            chart.data = [@foreach($venorden as $c) {
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
            chart.data = [@foreach($tipo_pagos as $c) {
                    "descripcion": "{{$c->nombre_pagos}}",
                    "total": "{{round($c->valor,2)}}"
                },
                @endforeach
            ];

            var chart = am4core.create("chartdiv3", am4charts.PieChart);
            chart.hiddenState.properties.opacity = 0; // this creates initial fade-in
            chart.data = [@foreach($tipo_pagos as $c) {
                    "descripcion": "{{$c->nombre_pagos}}",
                    "total": "{{round($c->valor,2)}}"
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

            chart.data = [@foreach($venorden as $c) {
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
    @else
        am4core.ready(function() {

            // Themes begin
            am4core.useTheme(am4themes_animated);
            // Themes end



            var chart = am4core.create("chartdiv", am4charts.XYChart);

            // some extra padding for range labels
            chart.paddingBottom = 50;

            chart.cursor = new am4charts.XYCursor();
            chart.scrollbarX = new am4core.Scrollbar();

            // will use this to store colors of the same items
            var colors = {};

            var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
            categoryAxis.dataFields.category = "category";
            categoryAxis.renderer.minGridDistance = 60;
            categoryAxis.renderer.grid.template.location = 0;
            categoryAxis.dataItems.template.text = "{realName}";
            categoryAxis.adapter.add("tooltipText", function(tooltipText, target) {
                return categoryAxis.tooltipDataItem.dataContext.realName;
            })

            var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
            valueAxis.tooltip.disabled = true;
            valueAxis.min = 0;

            // single column series for all data
            var columnSeries = chart.series.push(new am4charts.ColumnSeries());
            columnSeries.columns.template.width = am4core.percent(80);
            columnSeries.tooltipText = "{provider}: {realName}, {valueY}";
            columnSeries.dataFields.categoryX = "category";
            columnSeries.dataFields.valueY = "value";

            // second value axis for quantity
            var valueAxis2 = chart.yAxes.push(new am4charts.ValueAxis());
            valueAxis2.renderer.opposite = true;
            valueAxis2.syncWithAxis = valueAxis;
            valueAxis2.tooltip.disabled = true;

            // quantity line series
            var lineSeries = chart.series.push(new am4charts.LineSeries());
            lineSeries.tooltipText = "{valueY}";
            lineSeries.dataFields.categoryX = "category";
            lineSeries.dataFields.valueY = "quantity";
            lineSeries.yAxis = valueAxis2;
            lineSeries.bullets.push(new am4charts.CircleBullet());
            lineSeries.stroke = chart.colors.getIndex(13);
            lineSeries.fill = lineSeries.stroke;
            lineSeries.strokeWidth = 2;
            lineSeries.snapTooltip = true;

            // when data validated, adjust location of data item based on count
            lineSeries.events.on("datavalidated", function() {
                lineSeries.dataItems.each(function(dataItem) {
                    // if count divides by two, location is 0 (on the grid)
                    if (dataItem.dataContext.count / 2 == Math.round(dataItem.dataContext.count / 2)) {
                        dataItem.setLocation("categoryX", 0);
                    }
                    // otherwise location is 0.5 (middle)
                    else {
                        dataItem.setLocation("categoryX", 0.5);
                    }
                })
            })

            // fill adapter, here we save color value to colors object so that each time the item has the same name, the same color is used
            columnSeries.columns.template.adapter.add("fill", function(fill, target) {
                var name = target.dataItem.dataContext.realName;
                if (!colors[name]) {
                    colors[name] = chart.colors.next();
                }
                target.stroke = colors[name];
                return colors[name];
            })
            

            var rangeTemplate = categoryAxis.axisRanges.template;
            rangeTemplate.tick.disabled = false;
            rangeTemplate.tick.location = 0;
            rangeTemplate.tick.strokeOpacity = 0.6;
            rangeTemplate.tick.length = 60;
            rangeTemplate.grid.strokeOpacity = 0.5;
            rangeTemplate.label.tooltip = new am4core.Tooltip();
            rangeTemplate.label.tooltip.dy = -10;
            rangeTemplate.label.cloneTooltip = false;

            ///// DATA
            var chartData = [];
            var lineSeriesData = [];
            var count=0;
            var data = {
                @foreach($query_meses as $key => $z)
                @php
                switch ($key) {
                    case '0':
                        $mes = "ENERO";
                        break;
                    case '1':
                        $mes = "FEBRERO";
                        break;
                    case '2':
                        $mes = "MARZO";
                        break;
                    case '3':
                        $mes = "ABRIL";
                        break;
                    case '4':
                        $mes = "MAYO";
                        break;
                    case '5':
                        $mes = "JUNIO";
                        break;
                    case '6':
                        $mes = "JULIO";
                        break;
                    case '7':
                        $mes = "AGOSTO";
                        break;
                    case '8':
                        $mes = "SEPTIEMBRE";
                        break;
                    case '9':
                        $mes = "OCTUBRE";
                        break;
                    case '10':
                        $mes = "NOVIEMBRE";
                        break;
                    case '11':
                        $mes = "DICIEMBRE";
                        break;
                }
                $acumulador=0;
                @endphp 
                @if(count($z)>0)
                "{{$mes}}": {
                    @foreach($z as $f)
                    @php 
                    $acumulador+=$f->total;
                    @endphp
                    "{{$f->nombre_seguro}}":"{{round($f->total,2)}}",
                    @endforeach
                    quantity: "{{round($acumulador,2)}}",
                },
                @endif
            
                @endforeach
            }

            // process data ant prepare it for the chart
            for (var providerName in data) {
                var providerData = data[providerName];

                // add data of one provider to temp array
                var tempArray = [];
                var count = 0;
                // add items
                for (var itemName in providerData) {
                    //console.log(itemName);
                    if (itemName != "quantity") {
                        
                        
                        // we generate unique category for each column (providerName + "_" + itemName) and store realName
                        tempArray.push({
                            category: providerName + "_" + itemName,
                            realName: itemName, 
                            value: providerData[itemName],
                            provider: providerName
                        })
                    }
                    count++;
                }
                // sort temp array
                tempArray.sort(function(a, b) {
                    if (a.value > b.value) {
                        return 1;
                    } else if (a.value < b.value) {
                        return -1
                    } else {
                        return 0;
                    }
                })
                //console.log("entra en array");
                //console.log("aqui"+count);
                // add quantity and count to middle data item (line series uses it)
                //console.log(tempArray);
                var lineSeriesDataIndex = Math.floor(count / 2);
                //console.log(lineSeriesDataIndex);
                tempArray[lineSeriesDataIndex].quantity = providerData.quantity;
                tempArray[lineSeriesDataIndex].count = count;
                // push to the final data
                am4core.array.each(tempArray, function(item) {
                    chartData.push(item);
                })

                // create range (the additional label at the bottom)
                var range = categoryAxis.axisRanges.create();
                range.category = tempArray[0].category;
                range.endCategory = tempArray[tempArray.length - 1].category;
                range.label.text = tempArray[0].provider;
                range.label.dy = 30;
                range.label.truncate = true;
                range.label.fontWeight = "bold";
                range.label.tooltipText = tempArray[0].provider;

                range.label.adapter.add("maxWidth", function(maxWidth, target) {
                    var range = target.dataItem;
                    var startPosition = categoryAxis.categoryToPosition(range.category, 0);
                    var endPosition = categoryAxis.categoryToPosition(range.endCategory, 1);
                    var startX = categoryAxis.positionToCoordinate(startPosition);
                    var endX = categoryAxis.positionToCoordinate(endPosition);
                    return endX - startX;
                })
            }

            chart.data = chartData;


            // last tick
            var range = categoryAxis.axisRanges.create();
            range.category = chart.data[chart.data.length - 1].category;
            range.label.disabled = true;
            range.tick.location = 1;
            range.grid.location = 1;



        }); // end am4core.ready()
    @endif
</script>
