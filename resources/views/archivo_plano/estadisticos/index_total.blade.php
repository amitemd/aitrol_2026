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
            <label> Estadisticos Convenios Publicos por Convenio</label>
        </div>
        @php 
            $mes['01'] = 'Enero';$mes['02'] = 'Febrero';$mes['03'] = 'Marzo';$mes['04'] = 'Abril';$mes['05'] = 'Mayo';$mes['06'] = 'Junio';$mes['07'] = 'Julio';$mes['08'] = 'Agosto';$mes['09'] = 'Septiembre';$mes['10'] = 'Octubre';$mes['11'] = 'Noviembre';$mes['12'] = 'Diciembre';
        @endphp 
        <div class="box-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Convenio Iess-Gastroclinica</h5>
                    <div class="table table-responsive col-md-12" style="margin-top: 10px;">
                        <table id="example2" class="table table-hover dataTable" role="grid" aria-describedby="example2_info">
                            <thead>
                                <tr>
                                    <th>Año - Mes</th>
                                    <th>Valor</th>
                                </tr>

                            </thead>
                            <tbody>
                                @foreach($arr_iess_gastro as $key => $value)
                                @php $anio = substr($key,2,4); $nmes = substr($key,0,2); @endphp
                                <tr>
                                    <td>{{$anio}}-{{$mes[$nmes]}}</td>
                                    <td style="text-align: right;">$ {{number_format(round($value,2),2,'.',',')}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>
                </div>
            </div> 
           
            <div class="col-md-6">
                <h5>Convenio Iess-Carlos Robles</h5>
                <div class="table table-responsive col-md-12" style="margin-top: 10px;">
                    <table id="example2" class="table table-hover dataTable" role="grid" aria-describedby="example2_info">
                        <thead>
                            <tr>
                                <th>Año - Mes</th>
                                <th>Valor</th>
                            </tr>

                        </thead>
                        <tbody>
                            @foreach($arr_iess_robles as $key => $value)
                            @php $anio = substr($key,2,4); $nmes = substr($key,0,2); @endphp
                            <tr>
                                <td>{{$anio}}-{{$mes[$nmes]}}</td>
                                <td style="text-align: right;">$ {{number_format(round($value,2),2,'.',',')}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
            </div>

            <div class="col-md-6">
                <h5>Convenio Msp-Carlos Robles</h5>
                <div class="table table-responsive col-md-12" style="margin-top: 10px;">
                    <table id="example2" class="table table-hover dataTable" role="grid" aria-describedby="example2_info">
                        <thead>
                            <tr>
                                <th>Año - Mes</th>
                                <th>Valor</th>
                            </tr>

                        </thead>
                        <tbody>
                            @foreach($arr_msp_gastro as $key => $value)
                            @php $anio = substr($key,2,4); $nmes = substr($key,0,2); @endphp
                            <tr>
                                <td>{{$anio}}-{{$mes[$nmes]}}</td>
                                <td style="text-align: right;">$ {{number_format(round($value,2),2,'.',',')}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
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


        </div>
    </div>
</section>
<script src="https://cdn.amcharts.com/lib/4/core.js"></script>
<script src="https://cdn.amcharts.com/lib/4/charts.js"></script>
<script src="https://cdn.amcharts.com/lib/4/themes/animated.js"></script>


@endsection