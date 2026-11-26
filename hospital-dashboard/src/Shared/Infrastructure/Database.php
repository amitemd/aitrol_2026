<?php

declare(strict_types=1);

namespace Hospital\Shared\Infrastructure;

use DateTimeImmutable;
use PDO;
use Throwable;

final class Database
{
    public static function open(string $path): PDO
    {
        $database = new PDO('sqlite:'.$path, options: [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
        $database->exec('PRAGMA busy_timeout = 5000');
        if ((int) $database->query('PRAGMA user_version')->fetchColumn() >= 1) {
            return $database;
        }

        $database->exec('BEGIN IMMEDIATE');
        try {
            if ((int) $database->query('PRAGMA user_version')->fetchColumn() === 0) {
                $database->exec(file_get_contents(dirname(__DIR__, 3).'/database/schema.sql'));
                self::seed($database);
                $database->exec('PRAGMA user_version = 1');
            }
            $database->exec('COMMIT');
        } catch (Throwable $error) {
            $database->exec('ROLLBACK');
            throw $error;
        }

        return $database;
    }

    private static function seed(PDO $database): void
    {
        $patient = $database->prepare('INSERT INTO patients (name, registered_on) VALUES (?, ?)');
        for ($number = 1; $number <= 48; $number++) {
            $patient->execute([sprintf('Paciente de prueba %02d', $number), '2026-11-26']);
        }

        $activity = $database->prepare('INSERT INTO patient_activity (year, month, total, admitted) VALUES (?, ?, ?, ?)');
        $totals = [110, 162, 208, 176, 234, 280, 251, 312, 282, 330, 294, 348];
        $admitted = [56, 92, 70, 106, 118, 89, 145, 121, 158, 131, 170, 153];
        foreach ([2025, 2026] as $year) {
            $offset = $year === 2025 ? 22 : 0;
            foreach ($totals as $index => $total) {
                $activity->execute([$year, $index + 1, $total - $offset, $admitted[$index] - $offset]);
            }
        }

        $schedule = $database->prepare('INSERT INTO schedule (day, starts_at, ends_at, title, kind) VALUES (?, ?, ?, ?, ?)');
        $start = new DateTimeImmutable('2026-11-16');
        $entries = [
            ['08:00', '08:45', 'Consulta de medicina interna', 'appointment'],
            ['09:30', '10:00', 'Seguimiento por teleconsulta', 'consultation'],
            ['11:00', '12:15', 'Procedimiento ambulatorio', 'surgery'],
            ['13:00', '13:30', 'Control de enfermería', 'appointment'],
        ];

        for ($offset = 0; $offset < 14; $offset++) {
            $day = $start->modify('+'.$offset.' days')->format('Y-m-d');
            foreach ($entries as $index => $entry) {
                if (($offset + $index) % ($offset >= 7 ? 4 : 3) !== 0) {
                    $schedule->execute([$day, ...$entry]);
                }
            }
        }
    }
}
