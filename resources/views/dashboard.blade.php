@extends('layouts.dashboard')

@php
    $today = now('America/Guayaquil')->locale('es')->startOfDay();
    $weekStart = $today->copy()->startOfWeek();
    $indicators = [
        ['label' => 'Citas agendadas', 'value' => '1.250', 'trend' => '4,8 %', 'icon' => 'calendar', 'tone' => 'primary'],
        ['label' => 'Teleconsultas', 'value' => '1.002', 'trend' => '40 %', 'icon' => 'phone', 'tone' => 'mint'],
        ['label' => 'Cirugías', 'value' => '60', 'trend' => '25 %', 'icon' => 'medical', 'tone' => 'lilac'],
        ['label' => 'Pacientes registrados', 'value' => '1.835', 'trend' => '2,1 %', 'icon' => 'users', 'tone' => 'sand'],
    ];
    $reports = [
        ['title' => 'Mantenimiento de la habitación 123', 'time' => 'Hace 5 min', 'detail' => 'Se solicitó revisar la ducha de la habitación 123.'],
        ['title' => 'Revisión de inventario en quirófano', 'time' => 'Hace 18 min', 'detail' => 'El equipo de quirófano solicitó verificar el stock de insumos.'],
    ];
@endphp

@section('content')
    <header class="page-heading">
        <div>
            <h1>{{ __('Panel general') }}</h1>
            <p>Resumen de la operación hospitalaria</p>
        </div>
        <time class="date-chip" datetime="{{ $today->toDateString() }}">
            <svg class="icon" aria-hidden="true"><use href="#icon-calendar"/></svg>
            {{ $today->translatedFormat('l, j \d\e F') }}
        </time>
    </header>

    <section class="metrics" id="pacientes" aria-label="Indicadores de los últimos siete días">
        @foreach ($indicators as $indicator)
            <article class="metric metric--{{ $indicator['tone'] }}">
                <div class="metric-heading">
                    <span class="metric-icon"><svg class="icon" aria-hidden="true"><use href="#icon-{{ $indicator['icon'] }}"/></svg></span>
                    <h2>{{ $indicator['label'] }}</h2>
                </div>
                <div class="metric-result">
                    <strong>{{ $indicator['value'] }}</strong>
                    <span class="trend {{ $indicator['tone'] === 'lilac' ? 'trend--down' : '' }}">
                        <svg class="icon" aria-label="{{ $indicator['tone'] === 'lilac' ? 'Descenso' : 'Aumento' }}" role="img"><use href="#icon-{{ $indicator['tone'] === 'lilac' ? 'down' : 'up' }}"/></svg>
                        {{ $indicator['trend'] }}
                    </span>
                </div>
                <p>Respecto a la semana anterior</p>
            </article>
        @endforeach
    </section>

    <div class="dashboard-grid">
        <section class="panel statistics" id="estadisticas" aria-labelledby="statistics-title">
            <header class="panel-heading statistics-heading">
                <h2 id="statistics-title">Estadísticas de pacientes</h2>
                <div class="periods" role="group" aria-label="Período de las estadísticas">
                    <button type="button" data-period="week" aria-pressed="false">Semana</button>
                    <button type="button" data-period="month" aria-pressed="false">Mes</button>
                    <button type="button" data-period="year" aria-pressed="true">Año</button>
                </div>
            </header>
            <div class="chart">
                <div class="chart-scale" aria-hidden="true"><span>3.000</span><span>2.000</span><span>1.000</span><span>0</span></div>
                <div class="chart-body">
                    <div class="chart-plot">
                        <svg id="patient-chart" viewBox="0 0 750 222" preserveAspectRatio="none" role="img" aria-label="Evolución anual de pacientes atendidos y hospitalizados">
                            <defs><linearGradient id="chart-fill" x2="0" y2="1"><stop stop-color="#527fba" stop-opacity=".22"/><stop offset="1" stop-color="#527fba" stop-opacity=".01"/></linearGradient></defs>
                            <path class="chart-grid-lines" d="M0 1H750 M0 74H750 M0 148H750 M0 221H750"/>
                            <path id="chart-area" fill="url(#chart-fill)"/>
                            <path id="chart-attended" class="chart-line chart-line--attended"/>
                            <path id="chart-admitted" class="chart-line chart-line--admitted"/>
                        </svg>
                    </div>
                    <div class="chart-labels" id="chart-labels" aria-hidden="true"></div>
                </div>
            </div>
            <div class="legend"><span><i class="legend-attended"></i>Atendidos</span><span><i class="legend-admitted"></i>Hospitalizados</span></div>
        </section>

        <section class="panel agenda" id="agenda" aria-labelledby="agenda-title" data-today="{{ $today->toDateString() }}">
            <header class="panel-heading">
                <h2 id="agenda-title">Agenda</h2>
                <svg class="icon panel-icon" aria-hidden="true"><use href="#icon-calendar"/></svg>
            </header>
            <div class="calendar">
                <p id="calendar-date">{{ $today->translatedFormat('j \d\e F, Y') }}</p>
                <div class="calendar-days" role="group" aria-label="Días de la semana">
                    @for ($offset = 0; $offset < 7; $offset++)
                        @php($day = $weekStart->copy()->addDays($offset))
                        <button type="button" data-day="{{ $day->toDateString() }}" data-label="{{ $day->translatedFormat('j \d\e F, Y') }}" aria-label="{{ $day->translatedFormat('l j \d\e F') }}" aria-pressed="{{ $day->isSameDay($today) ? 'true' : 'false' }}">
                            <small>{{ $day->translatedFormat('D') }}</small>
                            <strong>{{ $day->day }}</strong>
                        </button>
                    @endfor
                </div>
            </div>
            <div class="schedule" aria-live="polite">
                <div id="today-events">
                    <div class="appointment"><time datetime="10:00">10:00</time><div><strong>Consulta de odontología</strong><span>10:00 – 11:00</span></div></div>
                    <div class="appointment"><time datetime="12:00">12:00</time><div><strong>Procedimientos</strong><span>12:00 – 16:00</span></div></div>
                </div>
                <p class="empty-agenda" id="empty-agenda" hidden>Sin citas programadas para este día.</p>
            </div>
        </section>

        <section class="panel balance" id="balance" aria-labelledby="balance-title">
            <header class="panel-heading"><h2 id="balance-title">Balance</h2><svg class="icon panel-icon" aria-hidden="true"><use href="#icon-wallet"/></svg></header>
            <div class="balance-body">
                <div class="balance-total">
                    <div class="balance-ring" aria-label="87 por ciento de la meta de ingresos" role="img"><strong>87 %</strong></div>
                    <span>Saldo neto</span><strong>$136.450</strong>
                </div>
                <dl class="balance-breakdown">
                    <div><dt>Ingresos</dt><dd>$8.135.450</dd></div>
                    <div><dt>Egresos</dt><dd>$7.999.000</dd></div>
                </dl>
            </div>
        </section>

        <section class="panel rooms" id="habitaciones" aria-labelledby="rooms-title">
            <header class="panel-heading"><h2 id="rooms-title">Ocupación</h2><svg class="icon panel-icon" aria-hidden="true"><use href="#icon-bed"/></svg></header>
            <p class="room-summary"><strong>176</strong> camas ocupadas</p>
            <dl class="room-list">
                <div><dt>Sala general</dt><dd>124</dd></div>
                <div><dt>Habitación privada</dt><dd>52</dd></div>
            </dl>
        </section>

        <section class="panel reports" id="reportes" aria-labelledby="reports-title">
            <header class="panel-heading"><h2 id="reports-title">Reportes recientes</h2><svg class="icon panel-icon" aria-hidden="true"><use href="#icon-flag"/></svg></header>
            <div class="report-list">
                @foreach ($reports as $report)
                    <details class="report">
                        <summary>
                            <span class="report-icon"><svg class="icon" aria-hidden="true"><use href="#icon-flag"/></svg></span>
                            <span><strong>{{ $report['title'] }}</strong><small>{{ $report['time'] }}</small></span>
                            <svg class="icon report-arrow" aria-hidden="true"><use href="#icon-arrow"/></svg>
                        </summary>
                        <p>{{ $report['detail'] }}</p>
                    </details>
                @endforeach
            </div>
        </section>
    </div>
@endsection
