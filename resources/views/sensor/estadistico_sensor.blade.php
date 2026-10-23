@extends('sensor.encabezado')
@section('action-content')

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadístico de Temperatura</title>
</head>
<section>
    <div class="p-4 rounded-lg bg-white mt-4" style="height:auto">
        <div class="grid sm:grid-cols-2 md:grid-cols-4 gap-4">
            <div class="mb-5">
                <label for="fecha_desde" class="block mb-2 text-sm font-medium text-gray-900">Fecha desde</label>
                <input type="date" name="fecha_desde" id="fecha_desde" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
            </div>
            <div class="mb-5">
                <label for="hora_desde" class="block mb-2 text-sm font-medium text-gray-900">Hora desde</label>
                <input type="time" name="hora_desde" id="hora_desde" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
            </div>
            <div class="mb-5">
                <label for="fecha_hasta" class="block mb-2 text-sm font-medium text-gray-900">Fecha hasta</label>
                <input type="date" name="fecha_hasta" id="fecha_hasta" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
            </div>
            <div class="mb-5">
                <label for="hora_hasta" class="block mb-2 text-sm font-medium text-gray-900">Hora hasta</label>
                <input type="time" name="hora_hasta" id="hora_hasta" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
            </div>
        </div>
        <div class="flex justify-end">
            <button type="button" class="bg-blue-500 hover:bg-blue-700 px-4 py-2 text-white rounded font-semibold shadow-md duration-300 transition-all flex gap-2 items-center" onclick="buscador_sensor()">
                <i class="fa fa-search"></i>
                Buscar
            </button>
        </div>
        <div id="chartContainer1">
            <canvas id="temperatureChart" width="900" height="250"></canvas>
            <div class="temperature-info">
                <p><span id="maxTemperature">-</span></p>
                <p><span id="minTemperature">-</span></p>
                <p><span id="promedioTemperature">-</span></p>
            </div>
        </div>
        <br>
        <br>
        <div id="chartContainer2">
            <canvas id="humedadChart" width="900" height="250"></canvas>
            <div class="temperature-info">
                <p><span id="maxHumedad">-</span></p>
                <p><span id="minHumedad">-</span></p>
                <p><span id="promedioHumedad">-</span></p>
            </div>
        </div>

    </div>
</section>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src='{{ asset ("/hc4/js/jquery.js") }}' type="text/javascript"></script>
<script src='{{ asset ("/hc4/js/chart.min.js") }}'></script>
<script src='{{ asset ("/js/bootstrap-datetimepicker.js") }}'></script>
<script>
    var temperatureChart;

    function cargarEstadisticoSensor(data) {
        if (temperatureChart) {
            temperatureChart.destroy();
        }

        const temperatures = data.map(item => item.valor_temperatura);
        console.log(temperatures);
        const timestamps = data.map(item => item.created_at);


        const formattedDates = timestamps.map(timestamp => {
            const date = new Date(timestamp);
            const month = (date.getMonth() + 1).toString().padStart(2, '0');
            const day = date.getDate().toString().padStart(2, '0');
            const hour = date.getHours().toString().padStart(2, '0');
            const minutes = date.getMinutes().toString().padStart(2, '0');
            const seconds = date.getSeconds().toString().padStart(2, '0');
            return `${month}-${day} ${hour}:${minutes}:${seconds}`;
        });

        const ctx = document.getElementById('temperatureChart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: formattedDates,
                datasets: [{
                    label: 'Temperatura',
                    data: temperatures,
                    borderColor: 'blue',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: false
                        }
                    }]
                }
            }
        });


        const maxTemperature = Math.max(...temperatures);
        const minTemperature = Math.min(...temperatures);
        const sumTemperatures = temperatures.reduce((total, temp) => total + parseFloat(temp), 0);
        const averageTemperature = (sumTemperatures / temperatures.length).toFixed(2);
       


        $('#promedioTemperature').text(`Temperatura promedio: ${averageTemperature}`);
        $('#maxTemperature').text(`Temperatura máxima: ${maxTemperature}`);
        $('#minTemperature').text(`Temperatura mínima: ${minTemperature}`);

        $('#chartContainer1').show();
    }

    var humedadChart;

    function cargarEstadisticoHumedad(data) {

        if (humedadChart) {
            humedadChart.destroy();
        }

        const humedad = data.map(item => item.valor_humedad);

        const timestamps = data.map(item => item.created_at);

        const formattedDates = timestamps.map(timestamp => {
            const date = new Date(timestamp);
            const month = (date.getMonth() + 1).toString().padStart(2, '0');
            const day = date.getDate().toString().padStart(2, '0');
            const hour = date.getHours().toString().padStart(2, '0');
            const minutes = date.getMinutes().toString().padStart(2, '0');
            const seconds = date.getSeconds().toString().padStart(2, '0');
            return `${month}-${day} ${hour}:${minutes}:${seconds}`;
        });



        const ctx = document.getElementById('humedadChart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: formattedDates,
                datasets: [{
                    label: 'humedad',
                    data: humedad,
                    borderColor: '#F42541',
                    backgroundColor: 'rgba(208, 79, 146, 0.8)',
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: false
                        }
                    }]
                }
            }
        });

        const maxTemperature = Math.max(...humedad);
        const minTemperature = Math.min(...humedad);
        const sumTemperatures = humedad.reduce((total, temp) => total + parseFloat(temp), 0);
        const averageTemperature = (sumTemperatures / humedad.length).toFixed(2);


        $('#promedioHumedad').text(`Humedad promedio: ${averageTemperature}`);
        $('#maxHumedad').text(`Humedad máxima: ${maxTemperature}`);
        $('#minHumedad').text(`Humedad mínima: ${minTemperature}`);


        $('#chartContainer2').show();
    }



    function buscador_sensor() {
        var fecha_desde = $('#fecha_desde').val();
        var fecha_hasta = $('#fecha_hasta').val();
        var hora_desde = $('#hora_desde').val();
        var hora_hasta = $('#hora_hasta').val();


        $.ajax({
            type: "POST",
            url: "{{ route('sensor.buscar_data') }}",
            data: {
                fecha_desde: fecha_desde,
                fecha_hasta: fecha_hasta,
                hora_desde: hora_desde,
                hora_hasta: hora_hasta
            },
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(data) {

                cargarEstadisticoSensor(data);

                cargarEstadisticoHumedad(data);

            }
        });
    }
    $(document).ready(function() {
        $('#chartContainer1,#chartContainer2').hide();
    });
</script>
@endsection