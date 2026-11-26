<?php

declare(strict_types=1);

$escape = static fn (string|int $value): string => htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$money = static fn (int $amount): string => '$'.number_format($amount, 0, ',', '.');
$months = ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];
$weekdays = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
$dayLabel = $day->format('j').' de '.$months[(int) $day->format('n') - 1].' de '.$day->format('Y');
$weekStart = $day->modify('monday this week');
$schedule = $data['schedule'];
$operations = $data['operations'];
$finance = $operations['finance'];
$occupancy = $operations['capacity'] > 0 ? (int) round(100 * $operations['occupied'] / $operations['capacity']) : 0;
$metrics = [
    ['appointment', 'Citas programadas', 'calendar', '#agenda'],
    ['consultation', 'Teleconsultas', 'phone', '#agenda'],
    ['surgery', 'Procedimientos', 'medical', '#agenda'],
    ['patients', 'Pacientes registrados', 'users', '#pacientes'],
];
$symbols = [
    'pulse' => '♥', 'grid' => '▦', 'users' => '♟', 'calendar' => '▤', 'phone' => '☎',
    'medical' => '✚', 'money' => '$', 'bed' => '▰', 'report' => '⚑', 'search' => '⌕', 'plus' => '+',
];
$icon = static fn (string $key): string => '<span class="icon icon-'.$key.'" aria-hidden="true">'.$symbols[$key].'</span>';
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="theme-color" content="#102e35">
    <title>Panel hospitalario · Aitrol Clínico</title>
    <link rel="stylesheet" href="dashboard.css">
    <script src="dashboard.js" defer></script>
</head>
<body>
<a class="skip-link" href="#contenido">Saltar al contenido</a>
<div class="shell">
    <aside class="sidebar">
        <a class="brand" href="./"><?= $icon('pulse') ?><span>Aitrol <strong>Clínico</strong></span></a>
        <nav aria-label="Menú principal">
            <p class="nav-label">Atención</p>
            <a class="side-link" href="#contenido" aria-current="location"><?= $icon('grid') ?> Resumen</a>
            <a class="side-link" href="#pacientes"><?= $icon('users') ?> Pacientes</a>
            <a class="side-link" href="#agenda"><?= $icon('calendar') ?> Agenda clínica</a>
            <p class="nav-label">Gestión</p>
            <a class="side-link" href="#balance"><?= $icon('money') ?> Finanzas</a>
            <a class="side-link" href="#ocupacion"><?= $icon('bed') ?> Ocupación</a>
            <a class="side-link" href="#reportes"><?= $icon('report') ?> Incidencias</a>
        </nav>
        <div class="sidebar-note"><span></span> Entorno de demostración</div>
    </aside>
    <div class="workspace">
        <header class="topbar">
            <form class="search" action="./#pacientes" method="get" role="search">
                <?= $icon('search') ?>
                <input type="hidden" name="day" value="<?= $escape($state['day']) ?>">
                <input type="hidden" name="year" value="<?= $year ?>">
                <input type="search" name="q" value="<?= $escape($query) ?>" placeholder="Buscar pacientes…" aria-label="Buscar pacientes" maxlength="120">
            </form>
            <div class="top-actions">
                <button class="primary" type="button" data-register><?= $icon('plus') ?> <span>Registrar paciente</span></button>
                <a class="avatar" href="#reportes" aria-label="Consultar incidencias" title="Consultar incidencias">AC</a>
            </div>
        </header>
        <main id="contenido" class="content">
            <div class="intro">
                <div>
                    <h1>Panel hospitalario</h1>
                    <p>Actividad clínica y operativa de la semana</p>
                </div>
                <span class="date-tag"><?= $icon('calendar') ?> <?= $escape($dayLabel) ?></span>
            </div>

            <?php if ($flash !== null || $error !== null) { ?>
                <p class="notice <?= $error !== null ? 'notice-error' : '' ?>" role="<?= $error !== null ? 'alert' : 'status' ?>"><?= $escape($error ?? $flash) ?></p>
            <?php } ?>

            <section class="metrics" aria-label="Indicadores principales">
                <?php foreach ($metrics as $index => [$key, $title, $glyph, $anchor]) {
                    $isPatientMetric = $key === 'patients';
                    $value = $isPatientMetric ? $data['patients']['count'] : $schedule['current'][$key];
                    $difference = $isPatientMetric ? 0 : $value - $schedule['previous'][$key];
                    ?>
                    <a class="metric <?= $index === 0 ? 'metric-featured' : '' ?>" href="<?= $anchor ?>">
                        <span class="metric-icon"><?= $icon($glyph) ?></span>
                        <span class="metric-label"><?= $escape($title) ?></span>
                        <strong><?= $escape($value) ?></strong>
                        <?php if ($isPatientMetric) { ?>
                            <small>En el registro</small>
                        <?php } else { ?>
                            <small class="<?= $difference < 0 ? 'decrease' : '' ?>"><?= $difference > 0 ? '+' : '' ?><?= $difference ?> vs. semana anterior</small>
                        <?php } ?>
                    </a>
                <?php } ?>
            </section>

            <div class="middle">
                <section class="panel analytics" aria-labelledby="activity-title">
                    <header class="panel-head">
                        <div><h2 id="activity-title">Actividad de pacientes</h2><p>Atenciones e ingresos por mes</p></div>
                        <nav class="tabs" aria-label="Año de las estadísticas">
                            <?php foreach ([2025, 2026] as $option) { ?>
                                <a href="<?= $escape($link(['year' => $option], '#activity-title')) ?>" <?= $year === $option ? 'aria-current="true"' : '' ?>><?= $option ?></a>
                            <?php } ?>
                        </nav>
                    </header>
                    <canvas id="activity-chart" role="img" aria-label="Gráfico de atenciones e ingresos. El detalle por mes está debajo." data-series="<?= $escape(json_encode($data['patients']['activity'], JSON_THROW_ON_ERROR)) ?>"></canvas>
                    <div class="legend"><span><b></b> Atenciones</span><span><b></b> Ingresos</span></div>
                    <details class="chart-data">
                        <summary>Ver datos por mes</summary>
                        <table><thead><tr><th>Mes</th><th>Atenciones</th><th>Ingresos</th></tr></thead><tbody>
                        <?php foreach ($data['patients']['activity'] as $row) { ?>
                            <tr><td><?= $escape($months[$row['month'] - 1]) ?></td><td><?= $row['total'] ?></td><td><?= $row['admitted'] ?></td></tr>
                        <?php } ?>
                        </tbody></table>
                    </details>
                </section>

                <section class="panel agenda" id="agenda" aria-labelledby="agenda-title">
                    <header class="panel-head">
                        <div><h2 id="agenda-title">Agenda clínica</h2><p><?= $escape($dayLabel) ?></p></div>
                        <div class="week-nav">
                            <a href="<?= $escape($link(['day' => $day->modify('-7 days')->format('Y-m-d')], '#agenda')) ?>" aria-label="Semana anterior">‹</a>
                            <a href="<?= $escape($link(['day' => $day->modify('+7 days')->format('Y-m-d')], '#agenda')) ?>" aria-label="Semana siguiente">›</a>
                        </div>
                    </header>
                    <nav class="calendar" aria-label="Días de la semana">
                        <?php foreach ($weekdays as $offset => $label) {
                            $date = $weekStart->modify('+'.$offset.' days');
                            $dateKey = $date->format('Y-m-d');
                            ?>
                            <a href="<?= $escape($link(['day' => $dateKey], '#agenda')) ?>" <?= $state['day'] === $dateKey ? 'aria-current="date"' : '' ?>>
                                <small><?= $label ?></small><strong><?= $date->format('j') ?></strong>
                            </a>
                        <?php } ?>
                    </nav>
                    <ol class="event-list">
                        <?php foreach ($schedule['events'] as $event) { ?>
                            <li><time datetime="<?= $escape($event['starts_at']) ?>"><?= $escape($event['starts_at']) ?></time>
                                <div class="event event-<?= $escape($event['kind']) ?>">
                                    <strong><?= $escape($event['title']) ?></strong>
                                    <small><?= $escape($event['starts_at'].' – '.$event['ends_at']) ?></small>
                                </div>
                            </li>
                        <?php } ?>
                        <?php if ($schedule['events'] === []) { ?><li class="empty">Sin actividades programadas para este día.</li><?php } ?>
                    </ol>
                </section>
            </div>

            <div class="bottom">
                <section class="panel" id="balance" aria-labelledby="balance-title">
                    <header class="panel-head"><h2 id="balance-title">Balance operativo</h2><?= $icon('money') ?></header>
                    <strong class="big-number"><?= $money($finance['balance']) ?></strong>
                    <p class="muted">Resultado acumulado</p>
                    <dl class="finance"><div><dt>Ingresos</dt><dd><?= $money($finance['income']) ?></dd></div><div><dt>Egresos</dt><dd><?= $money($finance['expenses']) ?></dd></div></dl>
                </section>
                <section class="panel" id="ocupacion" aria-labelledby="occupancy-title">
                    <header class="panel-head"><h2 id="occupancy-title">Ocupación de camas</h2><?= $icon('bed') ?></header>
                    <div class="occupancy"><strong class="big-number"><?= $occupancy ?>%</strong><span><?= $operations['occupied'] ?> de <?= $operations['capacity'] ?> camas ocupadas</span></div>
                    <progress value="<?= $occupancy ?>" max="100" aria-label="Ocupación total de camas"></progress>
                    <dl class="rooms">
                        <?php foreach ($operations['rooms'] as $room) { ?>
                            <div><dt><?= $escape($room['label']) ?></dt><dd><?= $room['occupied'] ?> / <?= $room['capacity'] ?></dd></div>
                        <?php } ?>
                    </dl>
                </section>
                <section class="panel reports" id="reportes" aria-labelledby="reports-title">
                    <header class="panel-head"><h2 id="reports-title">Incidencias recientes</h2><?= $icon('report') ?></header>
                    <?php foreach ($operations['reports'] as $report) { ?>
                        <details class="report">
                            <summary><strong><?= $escape($report['title']) ?></strong><small><?= $escape($report['area'].' · '.$report['status']) ?></small></summary>
                            <p><?= $escape($report['detail']) ?></p>
                        </details>
                    <?php } ?>
                </section>
            </div>
            <details class="panel patient-list" id="pacientes" <?= $query !== '' || $flash !== null ? 'open' : '' ?>>
                <summary>Registro de pacientes <span><?= $data['patients']['count'] ?></span></summary>
                <table>
                    <thead><tr><th>Paciente</th><th>Fecha de registro</th></tr></thead>
                    <tbody>
                    <?php foreach ($data['patients']['results'] as $patient) { ?>
                        <tr><td><?= $escape($patient['name']) ?></td><td><?= $escape($patient['registered_on']) ?></td></tr>
                    <?php } ?>
                    </tbody>
                </table>
                <?php if ($data['patients']['results'] === []) { ?><p class="empty">No se encontraron pacientes.</p><?php } ?>
            </details>
        </main>
    </div>
</div>
<dialog id="register-dialog" aria-labelledby="register-title" data-open="<?= $error !== null ? '1' : '0' ?>">
    <form method="post">
        <header><h2 id="register-title">Registrar paciente de prueba</h2><button type="button" data-close aria-label="Cerrar">×</button></header>
        <input type="hidden" name="csrf" value="<?= $escape($_SESSION['csrf']) ?>">
        <?php if ($error !== null) { ?><p role="alert" class="notice notice-error"><?= $escape($error) ?></p><?php } ?>
        <label for="patient-name">Nombre completo</label>
        <input id="patient-name" name="name" required minlength="3" maxlength="120" autocomplete="off">
        <button class="primary" type="submit">Guardar paciente</button>
    </form>
</dialog>
</body>
</html>
