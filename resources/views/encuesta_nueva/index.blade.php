@extends('laboratorio.encuesta_laboratorio.pregunta_labs.base')
@section('action-content')
    @php
        $array_uno = [];
        $array_dos = [];
    @endphp

    <section class="content">
        <div class="box">
            <div class="box-header">
                <form class="form-vertical" method="POST" action="{{ route('nueva.buscador') }}">
                    {{ csrf_field() }}
                    <input type="hidden" value="0" name="excel" id="excel">
                    <div class="form-group">
                        <div class="col-md-3">
                            <label>Fecha Desde</label>
                            <input type="date" value="{{ $desde }}" class="form-control" name="desde"
                                id="desde">
                        </div>
                        <div class="col-md-3">
                            <label>Fecha Hasta</label>
                            <input type="date" value="{{ $hasta }}" name="hasta" id="hasta"
                                class="form-control">
                        </div>
                        <div class="col-md-1">
                            <div>&nbsp;</div>
                            <button type="submit" onclick="document.getElementById('excel').value = 0" class="btn btn-primary">{{ trans('laboratorio.buscar') }}</button>
                        </div>
                        <div class="col-md-1">
                            <div>&nbsp;</div>
                            <button type="submit" onclick="document.getElementById('excel').value = 1" class="btn btn-success">Reporte Excel</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">

                        <br>
                        <h4><b>{{ trans('laboratorio.encuestas') }} Labs</b></h4>

                        <table border="2">
                            <thead>
                                <tr role="row">
                                    <th width="20%"
                                        style=" font-size: 16px; vertical-align: middle;border-color:black; text-align: center;">
                                        {{ trans('laboratorio.criteriosdeevaluacion') }}</th>
                                    <th width="5%" style="padding: 0 14px;border-color:black;text-align: center;">
                                        {{ trans('laboratorio.muybueno') }}</th>
                                    <th width="5%" style="padding: 0 14px;border-color:black;text-align: center;">
                                        {{ trans('laboratorio.bueno') }}</th>
                                    <th width="5%" style="padding: 0 14px;border-color:black;text-align: center;">
                                        {{ trans('laboratorio.nibuenonimalo') }} </th>
                                    <th width="5%" style="padding: 0 14px;border-color:black;text-align: center;">
                                        {{ trans('laboratorio.malo') }}</th>
                                    <th width="5%" style="padding: 0 14px;border-color:black;text-align: center;">
                                        {{ trans('laboratorio.muymalo') }}</th>
                                    <th width="5%" style="padding: 0 14px;border-color:black;text-align: center;">
                                        {{ trans('laboratorio.totalderespuestasrecibidasporpreguntas') }}</th>
                                    <th width="5%" style="padding: 0 14px;border-color:black;text-align: center;">
                                        {{ trans('laboratorio.indicadorsinponderarporpregunta') }}</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($array_resultados as $key => $val)
                                    @if (
                                        $key !== '¿El personal de toma de muestras le consultó su nombre antes de proceder a tomar las muestras?' &&
                                            $key !== '¿El personal de toma de muestras usó guantes, bata y gorro para el cabello?')
                                        <tr>

                                            <td style="text-align: center">
                                                {{ $key }}
                                            </td>


                                            @foreach ($val as $i)
                                                <td style="text-align: center">
                                                    {{ $i }}
                                                </td>
                                            @endforeach

                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                        <br>
                    </div>
                    @php
                        $array_uno = $array_resultados;
                        unset($array_uno["¿El personal de toma de muestras usó guantes, bata y gorro para el cabello?"]);
                        unset($array_uno["¿Las instalaciones estaban limpias y ordenadas?"]);

                    @endphp

                    <div class="col-md-12">
                        <center>
                            <canvas id="canvas2" style="max-width: 70%;"></canvas>
                        </center>
                    </div>

                    <div class="col-md-12">

                        <br>
                        <h4><b>{{ trans('laboratorio.encuestas') }} Labs</b></h4>

                        <table border="2">
                            <thead>
                                <tr role="row">
                                    <th width="20%"
                                        style=" font-size: 16px; vertical-align: middle;border-color:black; text-align: center;">
                                        {{ trans('laboratorio.criteriosdeevaluacion') }}</th>
                                    <th width="20%"
                                        style=" font-size: 16px; vertical-align: middle;border-color:black; text-align: center;">
                                        {{ trans('laboratorio.si') }}</th>
                                    <th width="5%" style="padding: 0 14px;border-color:black;text-align: center;">
                                        {{ trans('laboratorio.no') }}</th>
                                    <th width="5%" style="padding: 0 14px;border-color:black;text-align: center;">
                                        {{ trans('laboratorio.totalderespuestasrecibidasporpreguntas') }}</th>
                                    <th width="5%" style="padding: 0 14px;border-color:black;text-align: center;">
                                        {{ trans('laboratorio.indicadorsinponderarporpregunta') }}</th>


                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    
                                    $indices_a_eliminar = ['¿Las instalaciones estaban limpias y ordenadas?', '¿Cómo ha sido la amabilidad y respeto que recibió por parte del personal de toma de muestra?', '¿Cómo ha sido la explicación por parte del personal administrativo sobre los diversos medios para consultar los resultados?', '¿Cómo ha sido la información y resolución de dudas que recibió por parte del personal administrativo?'];
                                    foreach ($indices_a_eliminar as $indice) {
                                        unset($array_resultados[$indice]);
                                    }
                                    foreach ($array_resultados as $value => $y) {
                                        unset($array_resultados[$value][8]);
                                        unset($array_resultados[$value][5]);
                                        unset($array_resultados[$value][2]);
                                    }
                                    
                                @endphp
                                @foreach ($array_resultados as $clave => $iter)
                                    <tr>
                                        <td style="text-align: center">
                                            {{ $clave }}
                                        </td>
                                        @foreach ($iter as $i)
                                            <td style="text-align: center">
                                                {{ $i }}
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                                <tr role="row">
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td align="center"><b>{{ trans('laboratorio.indicadormensualsinponderacion') }}</b>
                                    </td>
                                    <td style="text-align: center">
                                        {{ number_format((float) $promedio, 2, '.', '') }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <br>
                    </div>
                    <div class="col-md-12">
                        <center>
                            <canvas id="canvas3" style="max-width: 70%;"></canvas>
                        </center>
                    </div>
                    @php
                        $array_dos = $array_resultados;
                    @endphp
                </div>
            </div>
            <script src="{{ asset('/plugins/datetimepicker/bootstrap-material-datetimepicker.js') }}"></script>
            <script src="{{ asset('/hc4/js/jquery.js') }}" type="text/javascript"></script>
            <script src="{{ asset('/hc4/js/chart.min.js') }}"></script>
            <script src="{{ asset('/js/bootstrap-datetimepicker.js') }}"></script>
            <script>
                function random_bg_color() {
                    var x = Math.floor(Math.random() * 256);
                    var y = Math.floor(Math.random() * 256);
                    var z = Math.floor(Math.random() * 256);
                    var bgColor = "rgb(" + x + "," + y + "," + z + ")";
                    return bgColor;
                }

                var barChartData = {
                    labels: ['Desde {{ $desde }} hasta {{ $hasta }}'],
                    datasets: [
                        @foreach ($array_uno as $pregunta => $respuestas)
                            {
                                label: '{{ $pregunta }}',
                                backgroundColor: random_bg_color(),
                                borderWidth: 1,
                                data: [
                                    {{ $respuestas['ponderacion'] }}
                                ],

                            },
                        @endforeach
                    ]
                };



                var ctx = document.getElementById('canvas2').getContext('2d');
                window.myBar = new Chart(ctx, {
                    type: 'bar',
                    data: barChartData,
                    options: {
                        responsive: true,
                        legend: {
                            position: 'bottom',
                            align: 'start',
                            fullWidth: 'true',
                            labels: {
                                //fontColor: 'rgb(255, 99, 132)',
                            },
                        },
                        title: {
                            display: true,
                            text: 'Encuestas LABS'
                        },
                        scales: {
                            yAxes: [{
                                display: true,
                                ticks: {
                                    beginAtZero: true,
                                    max: 100
                                }
                            }]
                        }
                    }
                });
            </script>

            <script>
                function random_bg_color() {
                    var x = Math.floor(Math.random() * 256);
                    var y = Math.floor(Math.random() * 256);
                    var z = Math.floor(Math.random() * 256);
                    var bgColor = "rgb(" + x + "," + y + "," + z + ")";
                    return bgColor;
                }

                var barChartData = {
                    labels: ['Desde {{ $desde }} hasta {{ $hasta }}'],
                    datasets: [
                        @foreach ($array_dos as $val)
                            {
                                label: '{{ $pregunta }}',
                                backgroundColor: random_bg_color(),
                                borderWidth: 1,
                                data: [
                                    {{ $val['ponderacion'] }}
                                ],

                            },
                        @endforeach
                    ],
                };



                var ctx = document.getElementById('canvas3').getContext('2d');
                window.myBar = new Chart(ctx, {
                    type: 'bar',
                    data: barChartData,
                    options: {
                        responsive: true,
                        legend: {
                            position: 'bottom',
                            align: 'start',
                            fullWidth: 'true',
                            labels: {
                                //fontColor: 'rgb(255, 99, 132)',
                            },
                        },
                        title: {
                            display: true,
                            text: 'Calificación LABS'
                        },
                        scales: {
                            yAxes: [{
                                display: true,
                                ticks: {
                                    beginAtZero: true,
                                    max: 100
                                }
                            }]
                        }
                    }
                });
            </script>
    </section>
@endsection
