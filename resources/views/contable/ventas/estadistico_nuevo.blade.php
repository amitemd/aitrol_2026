@extends('contable.acreedores.base')

@section('action-content')

<style>
     shapering-rendering {
        display: none !important;
    }
</style>

<section class="content">
    <div class="box">
        <div class="box header">
            <h3> {{trans('contableM.EstadisticosFacturadeVenta')}}</h3>
        </div>
        <div class="box-body">
            <form action="{{route('venta.nuevo_estadistico')}}" method="POST" id="per">
                {{ csrf_field() }}
                <div class="col-md-12" style="margin-top: 10px;">
                    <div class="col-md-4" style="margin-top: 10px;">
                        <label>{{trans('contableM.SELECCIONEANIO')}}</label>
                    </div>
                </div>
                <div class="col-md-12" style="margin-top: 10px;">
                    <div class="row">
                        
                        <!--lopez-->
                        <div class="col-md-4">
                            <select id="anio" name="anio" class="form-control">
                            <option value="">{{trans('contableM.SELECCIONEANIO')}}</option>
                                @for($i=2019;$i<2026;$i++)
                                    <option @if($request->anio==$i) selected="selected" @endif value="{{$i}}">{{$i}}</option>
                                @endfor
                            </select>
                        </div>
                        <!--termina año lopez-->
                        <div class="col-md-2">
                            <button type="button" onclick="return $('#per').submit();" class="btn btn-info btn-gray "> <i class="fa fa-search"></i> </button>
                        </div>
                    </div>
                </div>
                <div>
                </div>
                <div class="col-md-12" style="margin-top: 10px;">
                    <label>{{trans('contableM.DESGLOSEDEFACTURASDEVENTASPORMESDELANIO')}} {{$request->anio}}</label>
                </div>
                <div class="table table-responsive col-md-12" style="margin-top: 10px;">
                    <table id="example2" class="table table-hover dataTable" role="grid" aria-describedby="example2_info">
                        <thead style="background-color:#004AC1; color:white;">
                            <tr>
                                <th>#</th>
                                <th>{{trans('contableM.mes')}}</th>
                                <th style="text-align: right;">{{trans('contableM.particular')}}</th>
                                <th style="text-align: right;">{{trans('contableM.ValorPrivado')}}</th>
                                <th style="text-align: right;">{{trans('contableM.ValorPublico')}}</th>
                                <th style="text-align: right;">{{trans('contableM.EmpresaRelacionada')}}</th>
                                <th style="text-align: right;">{{trans('contableM.total')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $contadore=1;
                            $subtotal=0;
                            $total=0;
                            $particular=0;
                            $privado=0;
                            $publico=0;
                            $relacionado=0;
                            @endphp
                            @foreach($array_agrupado as $s)
                            @php
                            
                            $mes=trans('contableM.Enero');
                            if($s['mes']=='01'){
                            $mes=trans('contableM.Enero');
                            }elseif($s['mes']=='02'){
                            $mes=trans('contableM.Febrero');
                            }elseif($s['mes']=='03'){
                            $mes=trans('contableM.Marzo');
                            }elseif($s['mes']=='04'){
                            $mes=trans('contableM.Abril');
                            }elseif($s['mes']=='05'){
                            $mes=trans('contableM.Mayo');
                            }elseif($s['mes']=='06'){
                            $mes=trans('contableM.Junio');
                            }elseif($s['mes']=='07'){
                            $mes=trans('contableM.Julio');
                            }elseif($s['mes']=='08'){
                            $mes=trans('contableM.Agosto');
                            }elseif($s['mes']=='09'){
                            $mes=trans('contableM.Septiembre');
                            }elseif($s['mes']=='10'){
                            $mes=trans('contableM.Octubre');
                            }elseif($s['mes']=='11'){
                            $mes=trans('contableM.Noviembre');
                            }elseif($s['mes']=='12'){
                            $mes=trans('contableM.Diciembre');
                            }
                            
                            $subtotal=$s['particular']['subtotal0'] + $s['particular']['subtotal12']-$s['particular']['descuento'] +
                                      $s['privado']["subtotal0"] + 
                                      $s['privado']["subtotal12"] - $s['privado']["descuento"] +
                                      $s['publico']["subtotal0"] + $s['publico']["subtotal12"] - $s['publico']["descuento"] +
                                      $s['particular_relacionado']["subtotal0"] + $s['particular_relacionado']["subtotal12"] - $s['particular_relacionado']["descuento"];
                           
                            $total+=$subtotal;
                            $particular+=$s['particular']['subtotal0'] + $s['particular']['subtotal12'] - $s['particular']['descuento'];
                            $privado+=$s['privado']["subtotal0"] + $s['privado']["subtotal12"] - $s['privado']["descuento"];
                            $publico+=$s['publico']["subtotal0"] + $s['publico']["subtotal12"] - $s['publico']["descuento"];
                            $relacionado+=$s['particular_relacionado']["subtotal0"] + $s['particular_relacionado']["subtotal12"] - $s['particular_relacionado']["descuento"];
                           
                           
                            @endphp
                            <tr>
                                <td>{{$contadore}}</td>
                                <td>{{$mes}}</td>
                                <td style="text-align: right;">$ {{ number_format(($s['particular']['subtotal0'] + $s['particular']['subtotal12']) - $s['particular']['descuento'], 2, '.', ',') }}</td>
                                <td style="text-align: right;">$ {{ number_format(($s['privado']["subtotal0"] + $s['privado']["subtotal12"]) - $s['privado']["descuento"], 2, '.', ',') }}</td>
                                <td style="text-align: right;">$ {{ number_format(($s['publico']["subtotal0"] + $s['publico']["subtotal12"]) - $s['publico']["descuento"], 2, '.', ',') }}</td>
                                <td style="text-align: right;">$ {{ number_format(($s['particular_relacionado']["subtotal0"] + $s['particular_relacionado']["subtotal12"]) - $s['particular_relacionado']["descuento"], 2, '.', ',') }}</td>
                                <td style="text-align: right;">$ {{number_format(round($subtotal,2),2,'.',',')}}
                            </tr>
                            @php
                            $contadore++;
                            @endphp
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2"><b>{{trans('contableM.ResultadoTotal')}} </b></td>
                                <td style="text-align: right;"><b style="background-color: yellow;">$ {{number_format(round($particular,2),2,'.',',')}} </b></td>
                                <td style="text-align: right;"><b style="background-color: yellow;">$ {{number_format(round($privado,2),2,'.',',')}} </b></td>
                                <td style="text-align: right;"><b style="background-color: yellow;">$ {{number_format(round($publico,2),2,'.',',')}} </b></td>
                                <td style="text-align: right;"><b style="background-color: yellow;">$ {{number_format(round($relacionado,2),2,'.',',')}} </b></td>
                                <td style="text-align: right;"><b style="background-color: yellow;">$ {{number_format(round($total,2),2,'.',',')}} </b></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="col-md-12 col-xs-12" id="chartdiv" style="height: 400px; margin-top:25px;">
                </div>
                
                
                
        </div>
        </form>
    </div>
</section>
<script src="https://cdn.amcharts.com/lib/4/core.js"></script>
<script src="https://cdn.amcharts.com/lib/4/charts.js"></script>
<script src="https://cdn.amcharts.com/lib/4/themes/animated.js"></script>
<script>
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

        var data = {
            @foreach($array_agrupado as $s)
            @php

            $mes = trans('contableM.Enero');
            if ($s['mes'] == '01') {
                $mes = trans('contableM.Enero');
            }
            elseif($s['mes'] == '02') {
                $mes = trans('contableM.Febrero');
            }
            elseif($s['mes'] == '03') {
                $mes = trans('contableM.Marzo');
            }
            elseif($s['mes'] == '04') {
                $mes = trans('contableM.Abril');
            }
            elseif($s['mes'] == '05') {
                $mes = trans('contableM.Mayo');
            }
            elseif($s['mes'] == '06') {
                $mes = trans('contableM.Junio');
            }
            elseif($s['mes'] == '07') {
                $mes = trans('contableM.Julio');
            }
            elseif($s['mes'] == '08') {
                $mes = trans('contableM.Agosto');
            }
            elseif($s['mes'] == '09') {
                $mes = trans('contableM.Septiembre');
            }
            elseif($s['mes'] == '10') {
                $mes = trans('contableM.Octubre');
            }
            elseif($s['mes'] == '11') {
                $mes = trans('contableM.Noviembre');
            }
            elseif($s['mes'] == '12') {
                $mes = trans('contableM.Diciembre');
            }

            //dd($mes);
            
            @endphp "{{$mes}}": {
                @php
                $total = $s['publico']['subtotal0'] + $s['publico']['subtotal12'] - $s['publico']['descuento'] + $s['privado']['subtotal0'] + $s['privado']['subtotal12'] - $s['privado']['descuento'] + $s['particular']['subtotal0'] + $s['particular']['subtotal12'] - $s['particular']['descuento'] + $s['particular_relacionado']['subtotal0'] + $s['particular_relacionado']['subtotal12'] - $s['particular_relacionado']['descuento'];

                @endphp 
                "Publi.": "{{$s['publico']['subtotal0'] + $s['publico']['subtotal12'] - $s['publico']['descuento']}}",
                "Priv.": "{{$s['privado']['subtotal0'] + $s['privado']['subtotal12'] - $s['privado']['descuento']}}",
                "Part.": "{{$s['particular']['subtotal0'] + $s['particular']['subtotal12'] - $s['particular']['descuento']}}",
                "Rel.": "{{$s['particular_relacionado']['subtotal0'] + $s['particular_relacionado']['subtotal12'] - $s['particular_relacionado']['descuento']}}",
                "quantity": "{{number_format($total,2)}}"
            },
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
                if (itemName != "quantity") {
                    count++;
                    // we generate unique category for each column (providerName + "_" + itemName) and store realName
                    tempArray.push({
                        category: providerName + "_" + itemName,
                        realName: itemName,
                        value: providerData[itemName],
                        provider: providerName
                    })
                }
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

            // add quantity and count to middle data item (line series uses it)
            var lineSeriesDataIndex = Math.floor(count / 2);
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
            range.label.fontSize = 10;
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

        var chart = am4core.create("chartdiv2", am4charts.XYChart);

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
        categoryAxis.dataItems.label.fontSize = 10;
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
        rangeTemplate.label.tooltip.dy = -1;
        rangeTemplate.label.cloneTooltip = false;

        ///// DATA
        var chartData = [];
        var lineSeriesData = [];

        var data = {
            @foreach($array_agrupado_anio as $s)
            @php


            $mes = $request->anio;

            if ($s['anio'] == '03') {
                $mes = "Marzo";
            }
            elseif($s['anio'] == '04') {
                $mes = "Abril";
            }
            elseif($s['anio'] == '05') {
                $mes = "Mayo";
            }
            elseif($s['anio'] == '06') {
                $mes = "Junio";
            }
            elseif($s['anio'] == '07') {
                $mes = "Julio";
            }
            elseif($s['anio'] == '08') {
                $mes = "Agosto";
            }
            elseif($s['anio'] == '09') {
                $mes = "Septiembre";
            }
            elseif($s['anio'] == '10') {
                $mes = "Octubre";
            }
            elseif($s['anio'] == '11') {
                $mes = "Noviembre";
            }
            elseif($s['anio'] == '12') {
                $mes = "Diciembre";
            }
            @endphp "{{$mes}}": {
                @php
                $total = $s['privado'] + $s['publico'];
                @endphp 
                
                "Private": "{{$s['privado']}}",
                "Public": "{{$s['publico']}}",
                "quantity": "{{number_format($total,2)}}"
            },
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
                if (itemName != "quantity") {
                    count++;
                    // we generate unique category for each column (providerName + "_" + itemName) and store realName
                    tempArray.push({
                        category: providerName + "_" + itemName,
                        realName: itemName,
                        value: providerData[itemName],
                        provider: providerName
                    })
                }
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

            // add quantity and count to middle data item (line series uses it)
            var lineSeriesDataIndex = Math.floor(count / 2);
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
</script>

@endsection