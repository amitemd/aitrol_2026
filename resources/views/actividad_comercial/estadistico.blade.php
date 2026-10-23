@extends('actividad_comercial.base')
@section('action-content')
<style>
    .grid-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(316px, 1fr));
        gap: 20px;
        padding: 0px 10px;
    }

    .grid-buttons {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(5em, 1fr));
        gap: 5px;
        margin-top: 0px;
    }

    .banda {
        position: relative;
    }

    .banda::before {
        position: absolute;
        top: 11px;
        left: 204px;
        display: block;
        box-sizing: border-box;
        content: "Prospecto";
        color: #4B5563;
        font-size: 13px;
        text-transform: uppercase;
        text-align: center;
        font-weight: bold;
        height: 40px;
        width: 150px;
        border: 25px solid transparent;
        border-bottom: 25px solid rgb(147 197 253);
        line-height: 23px;
        transform: rotate(45deg);
    }

    .pagination>li>a {
        color: #4B5563 !important;
    }
</style>

@php
$fecha_desde = date('Y')."-01-01";
$fecha_hasta = date('Y-m-d');
@endphp
<div class="p-4 rounded-lg bg-white mt-4" style="height:auto; width: 100%;">
    <div class="grid sm:grid-cols-3 md:grid-cols-6 gap-4">
        <div class="mb-5">
            <label for="fecha_desde" class="block mb-2 text-sm font-medium text-gray-900">Fecha desde</label>
            <input type="date" name="fecha_desde" id="fecha_desde" value="{{$fecha_desde}}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
        </div>
        <div class="mb-5">
            <label for="fecha_desde" class="block mb-2 text-sm font-medium text-gray-900">Fecha hasta</label>
            <input type="date" name="fecha_hasta" id="fecha_hasta" value="{{$fecha_hasta}}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
        </div>
    </div>
    <div class="flex justify-start">
        <button type="button" class="bg-blue-500 hover:bg-blue-700 px-4 py-2 text-white rounded font-semibold shadow-md duration-300 transition-all flex gap-2 items-center" onclick="buscador()">
            <i class="fa fa-search"></i>
            Buscar
        </button>
    </div>

    <div class="container">

        <div id="chartContainer" style="display: none;">
            <canvas id="registrationChart" width="800" height="250"></canvas>
        </div>

        <br>
        <br>

        <div id="chartContainer2" style="display: none;">
            <canvas id="registrationChart2" width="800" height="250"></canvas>
        </div>
    </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>

    window.onload = buscador();
    let registrationChart;

    function cargarEstadistico(data) {

        if (registrationChart) {
            registrationChart.destroy();
        }

        const labels = Object.keys(data[0]);
        const counts = Object.values(data[0]).map(entry => entry.count);

        const labels2 = Object.keys(data[1]);
        const counts2 = Object.values(data[1]).map(entry => entry.count);

        const labels3 = Object.keys(data[2]);
        const counts3 = Object.values(data[2]).map(entry => entry.count);

        const ctx = document.getElementById('registrationChart').getContext('2d');
        registrationChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Clientes',
                    data: counts,
                    backgroundColor: 'rgba(255, 99, 132, 0.5)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 2
                }, {
                    label: 'Prospecto',
                    data: counts2,
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2
                }, {
                    label: 'Visitas',
                    data: counts3,
                    backgroundColor: 'rgba(39, 245, 131, 0.8)',
                    borderColor: 'rgba(145, 237, 186, 0.8)',
                    borderWidth: 2
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });
        document.getElementById('chartContainer').style.display = 'block';
    }


    let registrationChart2;

    function cargarEstadistico2(data) {
        if (registrationChart2) {
            registrationChart2.destroy();
        }

        const labels = ['Total'];
        const counts = [data.prospecto];
        const counts2 = [data.clientes];
        const counts3 = [data.vista];

        const ctx = document.getElementById('registrationChart2').getContext('2d');
        registrationChart2 = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                        label: 'Clientes',
                        data: counts2,
                        backgroundColor: 'rgba(255, 99, 132, 0.5)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 2
                    }, {
                        label: 'Prospecto',
                        data: counts,
                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 2,
                    },
                    {
                        label: 'Visitas',
                        data: counts3,
                        backgroundColor: 'rgba(39, 245, 131, 0.8)',
                        borderColor: 'rgba(145, 237, 186, 0.8)',
                        borderWidth: 2,
                    }
                ]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });
        document.getElementById('chartContainer2').style.display = 'block';
    }


    function buscador() {
        var fecha_desde = $('#fecha_desde').val();
        var fecha_hasta = $('#fecha_hasta').val();

        $.ajax({
            type: "POST",
            url: "{{ route('actividad_comercial.buscador_anio') }}",
            data: {
                fecha_desde: fecha_desde,
                fecha_hasta: fecha_hasta
            },
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(data) {
                cargarEstadistico(data);
                cargarEstadistico2(data[3]);
            }
        });
    }
</script>


@endsection