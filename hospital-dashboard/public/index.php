<?php

declare(strict_types=1);

use Hospital\Dashboard\Application\DashboardService;
use Hospital\Operations\Application\OperationsService;
use Hospital\Operations\Infrastructure\SqliteOperationsRepository;
use Hospital\Patients\Application\PatientService;
use Hospital\Patients\Infrastructure\SqlitePatientRepository;
use Hospital\Scheduling\Application\ScheduleService;
use Hospital\Scheduling\Infrastructure\SqliteScheduleRepository;
use Hospital\Shared\Infrastructure\Database;

spl_autoload_register(static function (string $class): void {
    $prefix = 'Hospital\\';
    if (str_starts_with($class, $prefix)) {
        require dirname(__DIR__).'/src/'.str_replace('\\', '/', substr($class, strlen($prefix))).'.php';
    }
});

date_default_timezone_set('America/Guayaquil');
session_name('aitrol_clinico_demo');
session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
session_start();
$_SESSION['csrf'] ??= bin2hex(random_bytes(32));

$databasePath = getenv('HOSPITAL_DEMO_DB') ?: dirname(__DIR__).'/hospital.sqlite';
$database = Database::open($databasePath);
$patients = new PatientService(new SqlitePatientRepository($database));
$dashboard = new DashboardService(
    $patients,
    new ScheduleService(new SqliteScheduleRepository($database)),
    new OperationsService(new SqliteOperationsRepository($database)),
);

$demoDay = new DateTimeImmutable('2026-11-26');
$dayInput = is_string($_GET['day'] ?? null) ? $_GET['day'] : '';
$selection = DateTimeImmutable::createFromFormat('!Y-m-d', $dayInput);
$day = $selection instanceof DateTimeImmutable && $selection->format('Y-m-d') === $dayInput ? $selection : $demoDay;
if ($day < $demoDay->modify('-365 days') || $day > $demoDay->modify('+365 days')) {
    $day = $demoDay;
}

$year = ($_GET['year'] ?? null) === '2025' ? 2025 : 2026;
$query = is_string($_GET['q'] ?? null) ? trim(substr($_GET['q'], 0, 120)) : '';
$state = ['day' => $day->format('Y-m-d'), 'year' => $year];
$link = static fn (array $changes = [], string $anchor = ''): string => '?'.http_build_query(array_replace($state, $changes)).$anchor;
$flash = $_SESSION['notice'] ?? null;
unset($_SESSION['notice']);
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf'] ?? null;
    if (! is_string($token) || ! hash_equals($_SESSION['csrf'], $token)) {
        http_response_code(403);
        exit('La sesión del formulario venció. Recarga la página.');
    }

    try {
        $patients->register(is_string($_POST['name'] ?? null) ? $_POST['name'] : '');
        $_SESSION['notice'] = 'Paciente de prueba registrado.';
        header('Location: '.$link([], '#pacientes'), true, 303);
        exit;
    } catch (InvalidArgumentException $exception) {
        http_response_code(422);
        $error = $exception->getMessage();
    }
}

$data = $dashboard->overview($day, $query, $year);
require dirname(__DIR__).'/views/dashboard.php';
